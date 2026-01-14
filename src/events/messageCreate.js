import axios from 'axios';
import { Events, PermissionFlagsBits, EmbedBuilder } from 'discord.js';
import userService from '../services/user.service.js';
import { askGemini } from '../services/gemini.service.js';
import trustService from '../services/trust.service.js';
import { all } from '../db/index.js';

const userMessageCooldown = new Map();
const userSpamTracking = new Map(); // { userId: { channels: Set(), startTime: timestamp } }
const autoModSpamCache = new Map(); // { userId: { count: 0, startTime: timestamp } }

export default {
    name: Events.MessageCreate,

    async execute(message) {
        if (message.author.bot || !message.guild) return;

        const content = message.content;

        // --- 🔍 UTILITY COMMANDS ([!google, !youtube, !avatar, !server, !ping]) ---
        if (content.startsWith('!google ')) {
            const query = content.replace('!google ', '').trim();
            if (query) {
                const searchUrl = `https://www.google.com/search?q=${encodeURIComponent(query)}`;
                return message.reply(`🔍 **Google Search:** ${query}\n${searchUrl}`);
            }
        }

        if (content.startsWith('!youtube ')) {
            const query = content.replace('!youtube ', '').trim();
            if (query) {
                const searchUrl = `https://www.youtube.com/results?search_query=${encodeURIComponent(query)}`;
                return message.reply(`📺 **YouTube Search:** ${query}\n${searchUrl}`);
            }
        }

        if (content.startsWith('!ping')) {
            return message.reply(`🏓 **Pong!** Latency: ${Date.now() - message.createdTimestamp}ms.`);
        }

        if (content.startsWith('!avatar')) {
            const user = message.mentions.users.first() || message.author;
            return message.reply({
                embeds: [
                    new EmbedBuilder()
                        .setTitle(`Avatar of ${user.username}`)
                        .setImage(user.displayAvatarURL({ size: 512, dynamic: true }))
                        .setColor('#6c63ff')
                        .setFooter({ text: `Requested by ${message.author.tag}` })
                ]
            });
        }

        if (content.startsWith('!server')) {
            const guild = message.guild;
            return message.reply({
                embeds: [
                    new EmbedBuilder()
                        .setTitle(guild.name)
                        .setThumbnail(guild.iconURL({ dynamic: true }))
                        .addFields(
                            { name: 'Owner', value: `<@${guild.ownerId}>`, inline: true },
                            { name: 'Members', value: `${guild.memberCount}`, inline: true },
                            { name: 'Created At', value: `<t:${Math.floor(guild.createdTimestamp / 1000)}:R>`, inline: false }
                        )
                        .setColor('#6c63ff')
                ]
            });
        }

        // --- 💰 ECONOMY SYSTEM ---
        const prefixes = ['!balance', '!bal', '!daily', '!weekly', '!work', '!pay', '!leaderboard', '!lb', '!rich'];
        if (prefixes.some(p => content.startsWith(p))) {
            const args = content.split(' ');
            const command = args[0].toLowerCase();

            try {
                // !balance
                if (command === '!balance' || command === '!bal') {
                    const target = message.mentions.users.first() || message.author;
                    const user = await userService.getUser(target.id, target.username);
                    return message.reply(`💰 **${target.username}** has **${user.coins.toLocaleString()} coins**.`);
                }

                // !daily
                if (command === '!daily') {
                    const claimed = await userService.claimDaily(message.author.id, message.author.username, 200);
                    if (claimed) {
                        return message.reply('🌞 **Daily Claimed!** You received **200 coins**.');
                    } else {
                        const check = await userService.checkDaily(message.author.id);
                        const hours = Math.ceil(check.remaining / (1000 * 60 * 60));
                        return message.reply(`⏳ You can claim daily again in **${hours} hours**.`);
                    }
                }

                // !weekly
                if (command === '!weekly') {
                    const claimed = await userService.claimWeekly(message.author.id, message.author.username, 1000);
                    if (claimed) {
                        return message.reply('📅 **Weekly Claimed!** You received **1000 coins**.');
                    } else {
                        const check = await userService.checkWeekly(message.author.id);
                        const days = Math.ceil(check.remaining / (1000 * 60 * 60 * 24));
                        return message.reply(`⏳ You can claim weekly again in **${days} days**.`);
                    }
                }

                // !work
                if (command === '!work') {
                    // Custom Work Cooldown (1 Hour) check manually since it's not in service yet
                    const user = await userService.getUser(message.author.id, message.author.username);
                    const now = Date.now();
                    const cooldown = 60 * 60 * 1000;
                    const lastWork = user.last_work || 0;

                    if (now - lastWork < cooldown) {
                        const minutes = Math.ceil((cooldown - (now - lastWork)) / 60000);
                        return message.reply(`☕ You need to rest! You can work again in **${minutes} minutes**.`);
                    }

                    const jobs = [
                        { name: 'Programmer', salary: [50, 100], msg: 'You fixed a bug and got paid' },
                        { name: 'Freelancer', salary: [30, 80], msg: 'You finished a gig and earned' },
                        { name: 'Uber Driver', salary: [20, 60], msg: 'You drove a karen around and got' },
                        { name: 'Streamer', salary: [10, 150], msg: 'You got a donation of' },
                    ];
                    const job = jobs[Math.floor(Math.random() * jobs.length)];
                    const earnings = Math.floor(Math.random() * (job.salary[1] - job.salary[0])) + job.salary[0];

                    await userService.addCoins(message.author.id, message.author.username, earnings);
                    // Update last_work
                    const { query } = await import('../db/index.js');
                    await query('UPDATE users SET last_work = ? WHERE id = ?', [now, message.author.id]);

                    return message.reply(`💼 **${job.name}**: ${job.msg} **${earnings} coins**.`);
                }

                // !pay <user> <amount>
                if (command === '!pay') {
                    const target = message.mentions.users.first();
                    const amount = parseInt(args[2]);

                    if (!target || isNaN(amount) || amount <= 0) {
                        return message.reply('Usage: `!pay @user <amount>`');
                    }
                    if (target.id === message.author.id) return message.reply('You cannot pay yourself.');

                    const sender = await userService.getUser(message.author.id, message.author.username);
                    if (sender.coins < amount) return message.reply('💸 You are too poor to afford this.');

                    await userService.addCoins(message.author.id, message.author.username, -amount);
                    await userService.addCoins(target.id, target.username, amount);

                    return message.reply(`💸 You sent **${amount} coins** to ${target}.`);
                }

                // !leaderboard
                if (command === '!leaderboard' || command === '!lb' || command === '!rich') {
                    const topUsers = await (await import('../db/index.js')).all('SELECT * FROM users ORDER BY coins DESC LIMIT 10');
                    const embed = new EmbedBuilder()
                        .setTitle('🏆 Richest Users')
                        .setColor('#FFD700')
                        .setDescription(topUsers.map((u, i) => `${i + 1}. **${u.username}** - 💰 ${u.coins.toLocaleString()}`).join('\n'));
                    return message.reply({ embeds: [embed] });
                }

            } catch (e) {
                console.error('[Economy Error]', e);
                return message.reply('An error occurred performing this command.');
            }
        }



        // --- 🎂 BIRTHDAY COMMAND ---
        if (content.startsWith('!birthday') || content.startsWith('!ultah')) {
            const args = content.split(' ');
            if (args.length < 2) return message.reply('Usage: `!birthday DD-MM` (e.g. `!birthday 25-12`)');

            const dateStr = args[1];
            const parts = dateStr.split('-');
            if (parts.length !== 2) return message.reply('Invalid format. Use `DD-MM`.');

            const day = parseInt(parts[0]);
            const month = parseInt(parts[1]);

            if (isNaN(day) || isNaN(month) || day < 1 || day > 31 || month < 1 || month > 12) {
                return message.reply('Invalid date.');
            }

            try {
                const { query } = await import('../db/index.js');
                // upsert
                await query('INSERT INTO birthdays (guild_id, user_id, user_name, day, month, created_at, updated_at) VALUES (?, ?, ?, ?, ?, NOW(), NOW()) ON DUPLICATE KEY UPDATE day = VALUES(day), month = VALUES(month), user_name = VALUES(user_name)',
                    [message.guild.id, message.author.id, message.author.username, day, month]);

                return message.reply(`🎂 Birthday saved! I will wish you a happy birthday on **${day}-${month}**!`);
            } catch (e) {
                console.error('[Birthday Error]', e);
                return message.reply('Failed to save birthday.');
            }
        }

        // --- 🛠️ CUSTOM COMMANDS ---
        if (message.content.startsWith('!')) {
            const trigger = message.content.slice(1).split(' ')[0]; // Get the word after '!'
            try {
                console.log(`[DEBUG] Checking custom command: ${trigger} in guild ${message.guild.id}`);
                const commands = await all("SELECT * FROM custom_commands WHERE guild_id = ? AND `trigger` = ?", [message.guild.id, trigger]);

                if (commands && commands.length > 0) {
                    const cmd = commands[0];

                    if (cmd.is_embed) {
                        try {
                            const embedData = JSON.parse(cmd.embed_data);
                            const embed = {};
                            if (embedData.title) embed.title = embedData.title;
                            if (embedData.description) embed.description = embedData.description;
                            if (embedData.color) embed.color = parseInt(embedData.color.replace('#', ''), 16);
                            if (embedData.image) embed.image = { url: embedData.image };

                            await message.channel.send({ content: cmd.response || null, embeds: [embed] });
                        } catch (e) {
                            console.error('Failed to parse embed data:', e);
                            if (cmd.response) await message.channel.send(cmd.response);
                        }
                    } else {
                        await message.channel.send(cmd.response);
                    }
                    return; // Stop processing other logic
                }
            } catch (e) {
                console.error('Error fetching custom commands:', e);
            }
        }

        // --- 🛡️ AUTO MODERATION (Database Rules) ---
        try {
            // Check permissions (Admins/Mods bypass)
            if (!message.member.permissions.has(PermissionFlagsBits.ManageMessages)) {
                const rules = await all('SELECT * FROM automod_rules WHERE guild_id = ? AND enabled = 1', [message.guild.id]);

                if (rules && rules.length > 0) {
                    for (const rule of rules) {
                        let violation = false;

                        // 1. Bad Words
                        if (rule.trigger_type === 'bad_word' && rule.trigger_content) {
                            const words = rule.trigger_content.split(',').map(w => w.trim().toLowerCase()).filter(w => w);
                            const content = message.content.toLowerCase();
                            if (words.some(w => content.includes(w))) {
                                violation = true;
                            }
                        }

                        // 2. Anti Link
                        if (rule.trigger_type === 'link') {
                            const linkRegex = /(https?:\/\/[^\s]+)/g;
                            if (linkRegex.test(message.content)) {
                                violation = true;
                            }
                        }

                        // 3. Spams (5 msg in 5 sec)
                        if (rule.trigger_type === 'spam') {
                            const now = Date.now();
                            const limit = 5;
                            const window = 5000;
                            const userId = message.author.id;

                            if (!autoModSpamCache.has(userId)) {
                                autoModSpamCache.set(userId, { count: 1, startTime: now });
                            } else {
                                const data = autoModSpamCache.get(userId);
                                if (now - data.startTime > window) {
                                    data.count = 1;
                                    data.startTime = now;
                                } else {
                                    data.count++;
                                }

                                if (data.count > limit) {
                                    violation = true;
                                }
                            }
                        }

                        if (violation) {
                            // Execute Action
                            if (rule.action === 'delete') {
                                await message.delete().catch(() => { });
                                const msg = await message.channel.send(`⚠️ ${message.author}, pesanmu dihapus karena melanggar aturan server.`);
                                setTimeout(() => msg.delete().catch(() => { }), 5000);
                            }
                            else if (rule.action === 'timeout') {
                                await message.member.timeout(10 * 60 * 1000, 'AutoMod Violation').catch(() => { });
                                await message.delete().catch(() => { });
                                message.channel.send(`🔇 ${message.author} di-timeout selama 10 menit.`);
                            }
                            else if (rule.action === 'kick') {
                                await message.member.kick('AutoMod Violation').catch(() => { });
                                message.channel.send(`👢 ${message.author} dikick dari server.`);
                            }
                            else if (rule.action === 'ban') {
                                await message.member.ban({ reason: 'AutoMod Violation' }).catch(() => { });
                                message.channel.send(`🔨 ${message.author} dibanned dari server.`);
                            }

                            return; // Stop processing further for this message
                        }
                    }
                }
            }
        } catch (error) {
            console.error('[AutoMod Error]', error);
        }

        // --- 🔍 TRUST SCORE OBSERVER ---
        // Monitor for spam/flooding behavior and deduct trust score passively
        // Note: Real penalty logic is inside trustService.observeUserBehavior, we just report events here.
        if (message.mentions.users.size > 5) {
            await trustService.observeUserBehavior(message.author.id, 'spam'); // Mass mention (Wait mostly for DB consistency)
        }

        // --- 🤖 AI REPLY FEATURE ---
        // If user replies to the BOT, the bot should answer back contextually
        if (message.reference && message.reference.messageId) {
            try {
                const repliedMessage = await message.channel.messages.fetch(message.reference.messageId);

                // Check if the reply target is Me (The Bot)
                if (repliedMessage.author.id === message.client.user.id) {
                    await message.channel.sendTyping();

                    // Simple "context": Just sending the new query for now
                    // Improvement: We could send "Previous: [BotMsg] Current: [UserMsg]" to prompt
                    const response = await askGemini(message.author.username, message.content);

                    await message.reply(response);
                    return; // Stop processing other logic (spam/xp) for AI chats? Optional.
                }
            } catch (error) {
                console.error('Error handling reply context:', error);
            }
        }

        // --- 🤖 AUTOMATIONS (Message Triggers) ---
        try {
            const automations = await all('SELECT * FROM automations WHERE guild_id = ? AND event = ? AND is_active = 1', [message.guild.id, 'message_create']);
            if (automations && automations.length > 0) {
                for (const auto of automations) {
                    if (message.content.toLowerCase().includes(auto.trigger_value.toLowerCase())) {
                        // Action
                        if (auto.action_type === 'reply') {
                            await message.reply(auto.action_value);
                        } else if (auto.action_type === 'add_role') {
                            await message.member.roles.add(auto.action_value).catch(console.error);
                        } else if (auto.action_type === 'remove_role') {
                            await message.member.roles.remove(auto.action_value).catch(console.error);
                        }
                    }
                }
            }
        } catch (e) { console.error('Automation Error:', e); }

        // --- 🛡️ ANTI-SPAM CROSS CHANNEL CHECK ---
        const SPAM_WINDOW_MS = 15000; // 15 detik window
        const MAX_CHANNELS = 3;

        // Skip Admin/Mod from spam check
        if (!message.member.permissions.has(PermissionFlagsBits.ManageMessages)) {
            const userId = message.author.id;
            const now = Date.now();

            if (!userSpamTracking.has(userId)) {
                userSpamTracking.set(userId, { channels: new Set(), startTime: now });
            }

            const userData = userSpamTracking.get(userId);

            // Reset warning window if expired
            if (now - userData.startTime > SPAM_WINDOW_MS) {
                userData.channels.clear();
                userData.startTime = now;
            }

            userData.channels.add(message.channel.id);

            // DETECT SPAM
            if (userData.channels.size > MAX_CHANNELS) {
                // Find Moderator Role
                const modRole = message.guild.roles.cache.find(r =>
                    r.name.toLowerCase().includes('mod') ||
                    r.permissions.has(PermissionFlagsBits.KickMembers)
                );
                const modTag = modRole ? `<@&${modRole.id}>` : '@here';

                try {
                    await message.reply({
                        content: `🚨 **WOI SANTAI DONG!** ${message.author} \nJangan nyepam di banyak channel sekaligus elah! Ganggu banget bjir. \n\n${modTag} tolong pantau bocah ini!`
                    });

                    // Reset tracking to prevent double warn immediately
                    userSpamTracking.delete(userId);
                    return; // Stop processing XP/Coins for spammer
                } catch (e) {
                    console.error('Failed to warn spammer:', e);
                }
            }
        }

        // --- AFK CHECK LOGIC ---
        // 1. Check if sender is AFK -> Remove AFK
        const senderAfk = await userService.getAfkStatus(message.author.id);
        if (senderAfk && senderAfk.is_afk) {
            await userService.removeAfk(message.author.id);

            // Revert nickname if needed
            if (message.member && message.member.manageable && message.member.nickname?.startsWith('[AFK] ')) {
                const newNick = message.member.nickname.replace('[AFK] ', '');
                await message.member.setNickname(newNick).catch(() => { });
            }

            const afkDuration = Date.now() - senderAfk.afk_timestamp;
            const minutes = Math.floor(afkDuration / 60000);

            message.reply(`👋 **Welcome back, ${message.author.username}!**\nKamu AFK selama ${minutes} menit. Status AFK dicabut.`)
                .then(msg => setTimeout(() => msg.delete(), 5000))
                .catch(() => { });
        }

        // 2. Check if mentioned users are AFK
        if (message.mentions.users.size > 0) {
            // Processing in parallel using Promise.all
            const checks = message.mentions.users.map(async (targetUser) => {
                const afkStatus = await userService.getAfkStatus(targetUser.id);
                if (afkStatus && afkStatus.is_afk) {
                    const timeAgo = Math.floor(afkStatus.afk_timestamp / 1000);
                    const embed = {
                        description: `💤 **${targetUser.username} sedang AFK**\n📝 Alasan: ${afkStatus.afk_reason}\n⏳ Sejak: <t:${timeAgo}:R>`,
                        color: 0x95A5A6
                    };
                    await message.reply({ embeds: [embed] }).catch(() => { });
                }
            });
            await Promise.all(checks);
        }

        // --- EXISTING XP & COIN LOGIC ---
        const WEBHOOK_URL = process.env.WEBHOOK_URL;
        const WEBHOOK_SECRET = process.env.DISCORD_BOT_SECRET;
        const LARAVEL_API_URL = process.env.LARAVEL_API_URL || 'http://127.0.0.1:8000';

        const cooldownAmount = 60 * 1000;
        const now = Date.now();

        let shouldReward = false;
        if (userMessageCooldown.has(message.author.id)) {
            const expirationTime = userMessageCooldown.get(message.author.id) + cooldownAmount;
            if (now >= expirationTime) {
                shouldReward = true;
            }
        } else {
            shouldReward = true;
        }

        if (shouldReward) {
            userMessageCooldown.set(message.author.id, now);
            const coinsToAdd = Math.floor(Math.random() * 5) + 1;
            const xpToAdd = Math.floor(Math.random() * 15) + 10;

            try {
                // Add Coins Locally
                await userService.addCoins(message.author.id, message.author.username, coinsToAdd);

                // 💸 CHAT REWARD NOTIFICATION
                // Reply to user to notify reward (will appear in their notification tag)
                message.reply(`💸 **Caching!** Kamu dapet **${coinsToAdd} coins** dari aktif ngechat!`)
                    .then(msg => setTimeout(() => msg.delete(), 10000)) // Auto delete after 10s to reduce spam
                    .catch(() => { });

                // Add XP Locally
                const result = await userService.addXp(message.author.id, message.author.username, xpToAdd, message.author.avatar);

                if (result.leveledUp) {
                    let rewardMsg = '';

                    // Check for Role Rewards
                    try {
                        const rewards = await all('SELECT * FROM level_rewards WHERE guild_id = ? AND level <= ?', [message.guild.id, result.level]);

                        if (rewards && rewards.length > 0) {
                            const member = await message.guild.members.fetch(message.author.id);

                            for (const reward of rewards) {
                                if (!member.roles.cache.has(reward.role_id)) {
                                    await member.roles.add(reward.role_id).catch(e => console.error(`Failed to add role ${reward.role_id}:`, e.message));
                                    rewardMsg += `\n🎁 **Role Unlocked:** <@&${reward.role_id}>`;
                                }
                            }
                        }
                    } catch (e) {
                        console.error('Error processing level rewards:', e);
                    }

                    const levelEmbed = {
                        title: '🚀 **LEVEL UP ALERT!**',
                        description: `Gokil! Selamat bro **${message.author.username}**, kamu naik level!\n\n⭐️ **Level Baru:** \`${result.level}\`\n🔥 **Total XP:** \`${result.xp}\`\n\n*Makin aktif, makin sepuh!*${rewardMsg}`,
                        color: 0xFF00FF, // Neon Purple
                        thumbnail: { url: message.author.displayAvatarURL({ dynamic: true }) }
                    };
                    message.channel.send({ content: `Congrats ${message.author}! 🎉`, embeds: [levelEmbed] });
                }

            } catch (error) {
                console.error('Error updating local stats:', error.message);
            }
        }

        // --- Activity Logging ---
        if (WEBHOOK_URL) {
            axios.post(WEBHOOK_URL, {
                discord_id: message.author.id,
                event_type: 'user_activity'
            }, {
                headers: { 'X-Discord-Bot-Secret': WEBHOOK_SECRET },
                timeout: 2000 // Short timeout
            }).catch(() => { /* Ignore connection errors */ });
        }

        // --- Meaningful Message Rewards ---
        if (message.content.split(' ').length > 10) {
            await userService.addXp(message.author.id, message.author.username, 5);
        }

        // --- Auto Sync Games & Events ---
        const GAME_CHANNEL_ID = process.env.DISCORD_GAME_CHANNEL_ID || '1391274558514004019';
        const EVENT_CHANNEL_ID = process.env.DISCORD_EVENT_CHANNEL_ID || '1439538769148772372';
        const API_GAME_URL = process.env.API_GAME_URL || `${LARAVEL_API_URL}/api/discord/game`;
        const API_EVENT_URL = process.env.API_EVENT_URL || `${LARAVEL_API_URL}/api/discord/event`;

        if (message.channel.id === GAME_CHANNEL_ID) {
            const lines = message.content.split('\n');
            const title = lines[0] || 'Game Baru';
            const link = lines[1] || '';
            const description = lines.slice(2).join('\n');
            let image = null;
            if (message.attachments.size > 0) image = message.attachments.first().url;

            axios.post(API_GAME_URL, {
                title, link, description, image, discord_message_id: message.id
            }, { timeout: 3000 }).catch(() => { });
        }

        if (message.channel.id === EVENT_CHANNEL_ID) {
            const lines = message.content.split('\n');
            const title = lines[0] || 'Event Baru';
            const date = lines[1] || null;
            const description = lines.slice(2).join('\n');
            let image = null;
            if (message.attachments.size > 0) image = message.attachments.first().url;

            axios.post(API_EVENT_URL, {
                title, date, description, image, discord_message_id: message.id
            }, { timeout: 3000 }).catch(() => { });
        }
    }
};
