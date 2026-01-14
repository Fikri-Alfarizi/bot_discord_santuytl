import { all } from '../db/index.js';
import { ChannelType, PermissionFlagsBits } from 'discord.js';

export function startStatsUpdater(client) {
    // Run immediately on start
    updateStats(client);

    // Run every 10 minutes
    setInterval(() => {
        updateStats(client);
    }, 10 * 60 * 1000);
}

async function updateStats(client) {
    try {
        const statsChannels = await all('SELECT * FROM stats_channels');
        if (!statsChannels) return;

        for (const stat of statsChannels) {
            try {
                const guild = client.guilds.cache.get(stat.guild_id);
                if (!guild) continue;

                const channel = guild.channels.cache.get(stat.channel_id);
                if (!channel) {
                    // Channel might be deleted manually. Should we delete from DB?
                    // For now, ignore.
                    continue;
                }

                let count = 0;
                await guild.members.fetch(); // Ensure cache is fresh

                switch (stat.type) {
                    case 'members':
                        count = guild.memberCount;
                        break;
                    case 'humans':
                        count = guild.members.cache.filter(m => !m.user.bot).size;
                        break;
                    case 'bots':
                        count = guild.members.cache.filter(m => m.user.bot).size;
                        break;
                    case 'online':
                        count = guild.members.cache.filter(m => m.presence?.status === 'online' || m.presence?.status === 'dnd' || m.presence?.status === 'idle').size;
                        break;
                    case 'roles':
                        if (stat.data) {
                            const role = guild.roles.cache.get(stat.data);
                            if (role) count = role.members.size;
                        }
                        break;
                }

                const newName = stat.format.replace('{count}', count);

                if (channel.name !== newName) {
                    await channel.setName(newName);
                    console.log(`[Stats] Updated channel ${stat.channel_id} to "${newName}"`);
                }

            } catch (err) {
                console.error(`[Stats] Error updating channel ${stat.channel_id}:`, err);
            }
        }
    } catch (err) {
        console.error('[Stats Updater] Error:', err);
    }
}
