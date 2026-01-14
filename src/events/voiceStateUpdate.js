import { ChannelType, PermissionFlagsBits, Events } from 'discord.js';
import { get, query, all } from '../db/index.js';

export default {
    name: Events.VoiceStateUpdate,
    async execute(oldState, newState) {
        // Ignore if no channel change
        if (oldState.channelId === newState.channelId) return;

        const guild = newState.guild;
        const member = newState.member;
        if (!guild || !member) return;

        // --- 1. HANDLE JOINING (CREATE CHANNEL) ---
        if (newState.channelId) {
            try {
                // Check if Joined Channel is a Hub
                const config = await get('SELECT * FROM temp_channel_configs WHERE guild_id = ? AND hub_channel_id = ?', [guild.id, newState.channelId]);

                if (config) {
                    // Create New Channel
                    const channelName = config.default_name.replace('{user}', member.user.username);
                    const parentCategory = config.category_id && guild.channels.cache.get(config.category_id) ? config.category_id : null;

                    const newChannel = await guild.channels.create({
                        name: channelName,
                        type: ChannelType.GuildVoice,
                        parent: parentCategory,
                        permissionOverwrites: [
                            {
                                id: member.id,
                                allow: [PermissionFlagsBits.Connect, PermissionFlagsBits.ManageChannels],
                            },
                            {
                                id: guild.id,
                                allow: [PermissionFlagsBits.Connect],
                            },
                        ],
                    });

                    // Move Member
                    try {
                        await member.voice.setChannel(newChannel);
                    } catch (e) {
                        // Member might have disconnected instantly
                        await newChannel.delete();
                        return;
                    }

                    // Store in DB
                    await query('INSERT INTO active_temp_channels (guild_id, channel_id, owner_id, created_at, updated_at) VALUES (?, ?, ?, NOW(), NOW())',
                        [guild.id, newChannel.id, member.id]);

                    console.log(`[TempChannel] Created channel ${newChannel.id} for ${member.user.tag}`);
                }
            } catch (err) {
                console.error('[TempChannel] Error on Join:', err);
            }
        }

        // --- 2. HANDLE LEAVING (DELETE CHANNEL) ---
        if (oldState.channelId) {
            try {
                // Check if Left Channel is a Temp Channel
                const tempChannel = await get('SELECT * FROM active_temp_channels WHERE channel_id = ?', [oldState.channelId]);

                if (tempChannel) {
                    const channel = guild.channels.cache.get(oldState.channelId);

                    if (channel) {
                        // Check if empty (or only bots)
                        const humans = channel.members.filter(m => !m.user.bot).size;

                        if (humans === 0) {
                            await channel.delete();
                            await query('DELETE FROM active_temp_channels WHERE channel_id = ?', [oldState.channelId]);
                            console.log(`[TempChannel] Deleted channel ${oldState.channelId} (Empty)`);
                        } else {
                            // If owner left, maybe reassign owner? 
                            // For simplicty, keep original owner until empty.
                        }
                    } else {
                        // Channel already deleted on Discord side manually?
                        await query('DELETE FROM active_temp_channels WHERE channel_id = ?', [oldState.channelId]);
                    }
                }
            } catch (err) {
                console.error('[TempChannel] Error on Leave:', err);
            }
        }
        // --- 3. AUTOMATIONS (Voice Join) ---
        if (newState.channelId && newState.channelId !== oldState.channelId) {
            try {
                const automations = await all('SELECT * FROM automations WHERE guild_id = ? AND event = ? AND is_active = 1', [guild.id, 'voice_join']);

                if (automations && automations.length > 0) {
                    for (const auto of automations) {
                        if (auto.trigger_value === newState.channelId) {
                            if (auto.action_type === 'add_role') {
                                await member.roles.add(auto.action_value).catch(console.error);
                            } else if (auto.action_type === 'remove_role') {
                                await member.roles.remove(auto.action_value).catch(console.error);
                            }
                        }
                    }
                }
            } catch (err) {
                console.error('[Automation Voice Limit] Error:', err);
            }
        }
    },
};
