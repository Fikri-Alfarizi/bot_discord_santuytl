import express from 'express';
import client from '../config/discord.js';

const router = express.Router();

async function getGuild() {
    return process.env.DISCORD_GUILD_ID ? await client.guilds.fetch(process.env.DISCORD_GUILD_ID) : client.guilds.cache.first();
}

// POST /gacha/notify
router.post('/gacha/notify', async (req, res) => {
    try {
        const { user_id, username, item_name, item_rarity, item_image, discord_id } = req.body;
        const GACHA_CHANNEL_ID = process.env.DISCORD_GACHA_CHANNEL_ID || process.env.DISCORD_GAME_CHANNEL_ID;

        if (!GACHA_CHANNEL_ID) return res.json({ success: false, message: 'Gacha channel not configured' });

        const guild = await getGuild();
        if (!guild) return res.status(404).json({ error: 'No guild found' });

        const channel = await guild.channels.fetch(GACHA_CHANNEL_ID);
        if (!channel || !channel.send) return res.status(404).json({ error: 'Gacha channel not found' });

        const rarityConfig = {
            common: { color: 0x9CA3AF, emoji: '⚪', name: 'Common' },
            rare: { color: 0x3B82F6, emoji: '🔵', name: 'Rare' },
            epic: { color: 0xA855F7, emoji: '🟣', name: 'Epic' },
            legendary: { color: 0xFBBF24, emoji: '🟡', name: 'Legendary' },
            mythic: { color: 0xEF4444, emoji: '🔴', name: 'Mythic' }
        };

        const config = rarityConfig[item_rarity] || rarityConfig.common;
        let userMention = username;
        if (discord_id) userMention = `<@${discord_id}>`;

        const embed = {
            title: `${config.emoji} GACHA RESULT ${config.emoji}`,
            description: `${userMention} mendapatkan **${item_name}**!`,
            color: config.color,
            fields: [
                { name: 'Rarity', value: `${config.emoji} ${config.name}`, inline: true },
                { name: 'Item', value: item_name, inline: true }
            ],
            timestamp: new Date().toISOString(),
            footer: { text: 'Mystic Gacha System' }
        };

        if (item_image) embed.thumbnail = { url: item_image };
        if (item_rarity === 'legendary' || item_rarity === 'mythic') {
            embed.description = `🎉 **JACKPOT!** 🎉\n${userMention} mendapatkan **${item_name}**!\n✨ Selamat atas keberuntungan luar biasa! ✨`;
        }

        await channel.send({ embeds: [embed] });
        return res.json({ success: true, message: 'Gacha result posted to Discord' });
    } catch (err) {
        console.error('Error in /gacha/notify:', err);
        res.status(500).json({ error: err.message });
    }
});

// POST /tickets
router.post('/tickets', async (req, res) => {
    try {
        const { ticket_id, user, subject, message } = req.body;
        const SUPPORT_CHANNEL_ID = process.env.DISCORD_SUPPORT_CHANNEL_ID || 'YOUR_SUPPORT_CHANNEL_ID'; // Ensure this env is set
        const guild = await getGuild();
        if (!guild) return res.status(404).json({ error: 'No guild found' });

        try {
            const channel = await guild.channels.fetch(SUPPORT_CHANNEL_ID);
            if (!channel || !channel.send) return res.status(404).json({ error: 'Support channel not found' });

            const discordMsg = await channel.send({
                embeds: [{
                    title: `Tiket #${ticket_id}: ${subject}`,
                    description: message,
                    fields: [
                        { name: 'User', value: user, inline: true },
                        { name: 'Status', value: 'Open', inline: true }
                    ],
                    color: 0x7289da,
                    timestamp: new Date().toISOString()
                }]
            });
            return res.json({ success: true, discord_ticket_id: discordMsg.id });
        } catch (e) {
            return res.status(404).json({ error: 'Support channel error: ' + e.message });
        }
    } catch (err) {
        res.status(500).json({ error: err.message });
    }
});

// POST /quest/notify
router.post('/quest/notify', async (req, res) => {
    try {
        const { type, user_id, username, discord_id, quest_name, quest_difficulty, quest_description, rewards } = req.body;
        const QUEST_CHANNEL_ID = process.env.DISCORD_QUEST_CHANNEL_ID || process.env.DISCORD_GAME_CHANNEL_ID;

        if (!QUEST_CHANNEL_ID) return res.json({ success: false, message: 'Quest channel not configured' });

        const guild = await getGuild();
        if (!guild) return res.status(404).json({ error: 'No guild found' });

        const channel = await guild.channels.fetch(QUEST_CHANNEL_ID);

        const difficultyConfig = {
            easy: { color: 0x10B981, emoji: '⭐', name: 'Easy' },
            medium: { color: 0xF59E0B, emoji: '⭐⭐', name: 'Medium' },
            hard: { color: 0xEF4444, emoji: '⭐⭐⭐', name: 'Hard' },
            legendary: { color: 0xA855F7, emoji: '👑', name: 'Legendary' }
        };

        const config = difficultyConfig[quest_difficulty] || difficultyConfig.easy;
        let userMention = username;
        if (discord_id) userMention = `<@${discord_id}>`;

        let embed;
        if (type === 'started') {
            embed = {
                title: `🎯 QUEST STARTED`,
                description: `${userMention} memulai quest **${quest_name}**!`,
                color: 0x3B82F6,
                fields: [
                    { name: 'Quest', value: quest_name, inline: true },
                    { name: 'Difficulty', value: `${config.emoji} ${config.name}`, inline: true }
                ],
                footer: { text: 'Semangat menyelesaikan quest!' },
                timestamp: new Date().toISOString()
            };
        } else if (type === 'completed') {
            const rewardText = [];
            if (rewards.xp) rewardText.push(`${rewards.xp} XP`);
            if (rewards.coins) rewardText.push(`${rewards.coins} Coins`);

            embed = {
                title: `✅ QUEST COMPLETED!`,
                description: `🎉 ${userMention} menyelesaikan **${quest_name}**!`,
                color: 0x10B981,
                fields: [
                    { name: 'Quest', value: quest_name, inline: true },
                    { name: 'Difficulty', value: `${config.emoji} ${config.name}`, inline: true },
                    { name: 'Rewards', value: rewardText.join(', ') || 'No rewards', inline: false }
                ],
                footer: { text: 'Quest System' },
                timestamp: new Date().toISOString()
            };
            if (quest_difficulty === 'legendary') {
                embed.description = `👑 **LEGENDARY QUEST COMPLETED!** 👑\n${userMention} menyelesaikan **${quest_name}**!\n✨ Prestasi luar biasa! ✨`;
            }
        }
        await channel.send({ embeds: [embed] });
        res.json({ success: true, message: 'Quest notification posted' });
    } catch (err) {
        res.status(500).json({ error: err.message });
    }
});

// POST /lucky-wheel/notify
router.post('/lucky-wheel/notify', async (req, res) => {
    try {
        const { user_id, username, discord_id, reward_label, reward_type, reward_amount, is_jackpot } = req.body;
        const GAME_CHANNEL_ID = process.env.DISCORD_GAME_CHANNEL_ID;
        if (!GAME_CHANNEL_ID) return res.json({ success: false, message: 'Game channel not configured' });

        const guild = await getGuild();
        if (!guild) return res.status(404).json({ error: 'No guild found' });
        const channel = await guild.channels.fetch(GAME_CHANNEL_ID);

        let userMention = username;
        if (discord_id) userMention = `<@${discord_id}>`;

        let color = 0x3B82F6;
        let title = '🎡 LUCKY WHEEL SPIN!';
        let description = `${userMention} memutar roda keberuntungan!`;

        if (is_jackpot) {
            color = 0xF59E0B;
            title = '🎰 JACKPOT WINNER! 🎰';
            description = `🎉 **LUAR BIASA!** ${userMention} mendapatkan JACKPOT! 🎉`;
        } else if (reward_type === 'coins') {
            color = 0xEAB308;
        } else if (reward_type === 'xp') {
            color = 0xA855F7;
        }

        const embed = {
            title: title,
            description: description,
            color: color,
            fields: [
                { name: 'Reward', value: `**${reward_label}**`, inline: true },
                { name: 'Type', value: reward_type.toUpperCase(), inline: true }
            ],
            footer: { text: 'Daily Lucky Wheel' },
            timestamp: new Date().toISOString()
        };

        await channel.send({ embeds: [embed] });
        res.json({ success: true });
    } catch (err) {
        res.status(500).json({ error: err.message });
    }
});

// POST /game/notify
router.post('/game/notify', async (req, res) => {
    try {
        const { user_id, username, discord_id, game_name, result_text, rewards, color_hex } = req.body;
        const GAME_CHANNEL_ID = process.env.DISCORD_GAME_CHANNEL_ID;

        if (!GAME_CHANNEL_ID) return res.json({ success: false, message: 'Game channel not configured' });

        const guild = await getGuild();
        if (!guild) return res.status(404).json({ error: 'No guild found' });
        const channel = await guild.channels.fetch(GAME_CHANNEL_ID);

        let userMention = username;
        if (discord_id) userMention = `<@${discord_id}>`;

        const embed = {
            title: `🎮 ${game_name}`,
            description: `${userMention} ${result_text}`,
            color: color_hex ? parseInt(color_hex.replace('#', ''), 16) : 0x3B82F6,
            fields: [],
            footer: { text: 'GameHub Arcade' },
            timestamp: new Date().toISOString()
        };

        if (rewards) {
            const rewardParts = [];
            if (rewards.coins) rewardParts.push(`💰 ${rewards.coins} Coins`);
            if (rewards.xp) rewardParts.push(`✨ ${rewards.xp} XP`);
            if (rewardParts.length > 0) {
                embed.fields.push({ name: 'Rewards', value: rewardParts.join(' | '), inline: true });
            }
        }

        await channel.send({ embeds: [embed] });
        res.json({ success: true });
    } catch (err) {
        res.status(500).json({ error: err.message });
    }
});

// POST /send-message
router.post('/send-message', async (req, res) => {
    const { channel_id, message } = req.body;
    if (!channel_id || !message) return res.status(400).json({ error: 'channel_id dan message wajib diisi' });
    try {
        const channel = await client.channels.fetch(channel_id);
        if (!channel || !channel.send) {
            return res.status(404).json({ error: 'Channel tidak ditemukan atau tidak bisa mengirim pesan' });
        }
        await channel.send(message);
        res.json({ success: true });
    } catch (err) {
        res.status(500).json({ error: err.message });
    }
});

// POST /send-dm
router.post('/send-dm', async (req, res) => {
    const { user_id, message } = req.body;
    if (!user_id || !message) return res.status(400).json({ error: 'user_id dan message wajib diisi' });
    try {
        const user = await client.users.fetch(user_id);
        await user.send(message);
        res.json({ success: true });
    } catch (err) {
        res.status(500).json({ error: err.message });
    }
});

export default router;
