import { scheduleDailyYap } from '../cron/dailyYap.js';
import { startStatsUpdater } from '../cron/statsUpdater.js';
import { startReminderScheduler } from '../cron/reminderScheduler.js';
import { startSocialAlerts } from '../cron/socialAlerts.js';
import { startGiveawayScheduler } from '../cron/giveawayEnd.js';
import { startBirthdayAnnouncer } from '../cron/birthdayAnnouncer.js';
import inviteService from '../services/invite.service.js';
import { all, query } from '../db/index.js';
import { EmbedBuilder, ActionRowBuilder, ButtonBuilder, ButtonStyle, ChannelType, PermissionsBitField, Events } from 'discord.js';

export default {
    name: Events.ClientReady,
    once: true,
    async execute(client) {
        console.log(`Bot is ready! Logged in as ${client.user.tag}`);
        startStatsUpdater(client);
        startReminderScheduler(client);
        startSocialAlerts(client);
        startStatsUpdater(client);
        startReminderScheduler(client);
        startSocialAlerts(client);
        startGiveawayScheduler(client);
        startBirthdayAnnouncer(client);

        // Initialize Invite Cache
        for (const [id, guild] of client.guilds.cache) {
            await inviteService.cacheInvites(guild);
        }

        // --- 📬 OUTBOX POLLER (IPC) ---
        // Checks for actions from dashboard every 5 seconds
        setInterval(async () => {
            try {
                const pendingActions = await all('SELECT * FROM bot_outbox WHERE processed = 0 LIMIT 5');

                if (pendingActions && pendingActions.length > 0) {
                    for (const action of pendingActions) {
                        try {
                            const guild = client.guilds.cache.get(action.guild_id);
                            if (!guild) throw new Error('Guild not found');

                            const payload = JSON.parse(action.payload);

                            if (action.type === 'ticket_panel') {
                                const channel = guild.channels.cache.get(action.channel_id);
                                if (channel) {
                                    const embed = new EmbedBuilder()
                                        .setTitle(payload.title)
                                        .setDescription(payload.description || 'Click to open ticket')
                                        .setColor(payload.color || '#6c63ff');

                                    const row = new ActionRowBuilder()
                                        .addComponents(
                                            new ButtonBuilder()
                                                .setCustomId('ticket_create')
                                                .setLabel(payload.button_text || 'Open Ticket')
                                                .setStyle(ButtonStyle.Primary)
                                                .setEmoji('🎫')
                                        );

                                    await channel.send({ embeds: [embed], components: [row] });
                                }
                            }

                            else if (action.type === 'poll_create') {
                                const channel = guild.channels.cache.get(action.channel_id);
                                if (channel) {
                                    const options = payload.options; // Array of strings
                                    const description = options.map((opt, i) => `${i + 1}️⃣ ${opt}`).join('\n\n');

                                    const embed = new EmbedBuilder()
                                        .setTitle(`📊 ${payload.question}`)
                                        .setDescription(description)
                                        .setColor(payload.color || '#6c63ff')
                                        .setFooter({ text: 'Total Votes: 0' })
                                        .setTimestamp();

                                    const row = new ActionRowBuilder();

                                    options.forEach((opt, i) => {
                                        row.addComponents(
                                            new ButtonBuilder()
                                                .setCustomId(`poll_vote_${i}`)
                                                .setLabel(`${i + 1}`)
                                                .setStyle(ButtonStyle.Secondary)
                                        );
                                    });

                                    await channel.send({ embeds: [embed], components: [row] });
                                }
                            }

                            else if (action.type === 'stats_create') {
                                // Create Voice Channel
                                const channel = await guild.channels.create({
                                    name: payload.format.replace('{count}', '...'),
                                    type: ChannelType.GuildVoice,
                                    permissionOverwrites: [
                                        {
                                            id: guild.id,
                                            deny: [PermissionsBitField.Flags.Connect], // Lock channel
                                            allow: [PermissionsBitField.Flags.ViewChannel],
                                        },
                                    ],
                                });

                                // Insert into DB
                                await query(
                                    'INSERT INTO stats_channels (guild_id, channel_id, type, format, data, created_at, updated_at) VALUES (?, ?, ?, ?, ?, NOW(), NOW())',
                                    [action.guild_id, channel.id, payload.type, payload.format, payload.data]
                                );
                            }

                            else if (action.type === 'create_giveaway') {
                                const { giveaway_id, channel_id, prize, description, end_at, winner_count } = payload;
                                const channel = guild.channels.cache.get(channel_id);
                                if (channel) {
                                    const embed = new EmbedBuilder()
                                        .setTitle('🎉 GIVEAWAY 🎉')
                                        .setDescription(`**Prize**: ${prize}\n\n${description || ''}\n\n🏆 **Winners**: ${winner_count}\n⏳ **Ends**: <t:${Math.floor(end_at / 1000)}:R>`)
                                        .setColor('#9b59b6')
                                        .setFooter({ text: 'Click the button below to join!' });

                                    const row = new ActionRowBuilder()
                                        .addComponents(
                                            new ButtonBuilder()
                                                .setCustomId(`giveaway_join_${giveaway_id}`)
                                                .setLabel('🎉 Join Giveaway')
                                                .setStyle(ButtonStyle.Primary)
                                        );

                                    const msg = await channel.send({ embeds: [embed], components: [row] });

                                    // Update DB with message_id
                                    await query('UPDATE giveaways SET message_id = ? WHERE id = ?', [msg.id, giveaway_id]);
                                    console.log(`[Giveaway] Created ${prize} in ${channel.name}`);
                                }
                            }

                            else if (action.type === 'stats_delete') {
                                const channel = guild.channels.cache.get(action.channel_id);
                                if (channel) {
                                    await channel.delete();
                                }
                            }

                            // Mark as processed
                            await query('UPDATE bot_outbox SET processed = 1 WHERE id = ?', [action.id]);
                            console.log(`[Outbox] Processed action ${action.id} (${action.type})`);

                        } catch (err) {
                            console.error(`[Outbox] Failed to process action ${action.id}:`, err);
                            // Mark processed even if failed to prevent infinite loop
                            await query('UPDATE bot_outbox SET processed = 1 WHERE id = ?', [action.id]);
                        }
                    }
                }
            } catch (e) {
                console.error('[Outbox Error]', e);
            }
        }, 5000);
    },
};
