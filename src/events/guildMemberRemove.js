import { Events, EmbedBuilder } from 'discord.js';
import { get } from '../db/index.js';

export default {
    name: Events.GuildMemberRemove,
    async execute(member) {
        const guild = member.guild;

        try {
            // 1. Fetch Settings
            const settings = await get('SELECT * FROM guild_settings WHERE guild_id = ?', [guild.id]);

            if (!settings) return;

            // 2. Goodbye Message
            if (settings.goodbye_enabled && settings.goodbye_channel_id) {
                const channel = guild.channels.cache.get(settings.goodbye_channel_id);
                if (channel) {
                    // Replace placeholders
                    const replaceVars = (text) => text
                        ? text.replace(/{user}/g, `<@${member.id}>`)
                            .replace(/{username}/g, member.user.username)
                            .replace(/{server}/g, guild.name)
                            .replace(/{membercount}/g, guild.memberCount)
                        : '';

                    const messageContent = replaceVars(settings.goodbye_message);
                    const payload = {};

                    if (messageContent) payload.content = messageContent;

                    // Embed
                    if (settings.goodbye_embed_enabled) {
                        const embed = new EmbedBuilder();

                        if (settings.goodbye_embed_title) embed.setTitle(replaceVars(settings.goodbye_embed_title));
                        if (settings.goodbye_embed_description) embed.setDescription(replaceVars(settings.goodbye_embed_description));
                        if (settings.goodbye_embed_color) embed.setColor(settings.goodbye_embed_color);

                        embed.setTimestamp();
                        payload.embeds = [embed];
                    }

                    if (payload.content || payload.embeds) {
                        await channel.send(payload).catch(console.error);
                    }
                }
            }

            console.log(`[Goodbye] Processed for ${member.user.tag} in ${guild.name}`);

        } catch (error) {
            console.error('[Goodbye Error]', error);
        }
    }
};
