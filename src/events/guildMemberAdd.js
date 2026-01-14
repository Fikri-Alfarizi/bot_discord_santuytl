import { Events, EmbedBuilder } from 'discord.js';
import { get, query } from '../db/index.js';
import inviteService from '../services/invite.service.js';

export default {
    name: Events.GuildMemberAdd,
    async execute(member) {
        const guild = member.guild;

        // --- 📨 INVITE TRACKER ---
        try {
            const usedInvite = await inviteService.findUsedInvite(guild);
            if (usedInvite) {
                const inviter = usedInvite.inviter;

                // Account Age Check (Fake Detection: < 3 days)
                const accountAge = Date.now() - member.user.createdTimestamp;
                const isFake = accountAge < (3 * 24 * 60 * 60 * 1000);

                const now = Math.floor(Date.now() / 1000);
                const existing = await get('SELECT * FROM invites WHERE inviter_id = ? AND invited_id = ?', [inviter.id, member.id]);
                if (existing) {
                    await query('UPDATE invites SET updated_at = ? WHERE inviter_id = ? AND invited_id = ?', [now, inviter.id, member.id]);
                } else {
                    await query(
                        `INSERT INTO invites (guild_id, inviter_id, invited_id, code, timestamp, is_valid, is_fake, created_at, updated_at) 
                         VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)`,
                        [guild.id, inviter.id, member.id, usedInvite.code, Date.now(), true, isFake, now, now]
                    );
                }

                console.log(`[Invite] ${member.user.tag} invited by ${inviter.tag} (Code: ${usedInvite.code})`);
            } else {
                // Unknown invite (Vanity URL or unsure)
                console.log(`[Invite] ${member.user.tag} joined via unknown invite.`);
            }
        } catch (e) {
            console.error('[Invite Error]', e);
        }

        try {
            // 1. Fetch Settings
            const settings = await get('SELECT * FROM guild_settings WHERE guild_id = ?', [guild.id]);

            if (!settings) return; // No config found

            // 2. Auto Role
            if (settings.auto_role_enabled && settings.auto_role_id) {
                const role = guild.roles.cache.get(settings.auto_role_id);
                if (role) {
                    await member.roles.add(role).catch(err => console.error(`Failed to assign auto role: ${err.message}`));
                }
            }

            // 3. Welcome Message (Channel)
            if (settings.welcome_enabled && settings.welcome_channel_id) {
                const channel = guild.channels.cache.get(settings.welcome_channel_id);
                if (channel) {
                    // Replace placeholders
                    const replaceVars = (text) => text
                        ? text.replace(/{user}/g, `<@${member.id}>`)
                            .replace(/{username}/g, member.user.username)
                            .replace(/{server}/g, guild.name)
                            .replace(/{membercount}/g, guild.memberCount)
                        : '';

                    const messageContent = replaceVars(settings.welcome_message);
                    const payload = {};

                    if (messageContent) payload.content = messageContent;

                    // Embed
                    if (settings.welcome_embed_enabled) {
                        const embed = new EmbedBuilder();

                        if (settings.welcome_embed_title) embed.setTitle(replaceVars(settings.welcome_embed_title));
                        if (settings.welcome_embed_description) embed.setDescription(replaceVars(settings.welcome_embed_description));
                        if (settings.welcome_embed_color) embed.setColor(settings.welcome_embed_color);
                        if (settings.welcome_embed_image) embed.setImage(settings.welcome_embed_image);
                        if (settings.welcome_embed_thumbnail) embed.setThumbnail(settings.welcome_embed_thumbnail);

                        embed.setTimestamp();

                        payload.embeds = [embed];
                    }

                    if (payload.content || payload.embeds) {
                        await channel.send(payload).catch(console.error);
                    }
                }
            }

            // 4. Welcome DM
            if (settings.welcome_dm_enabled && settings.welcome_dm_message) {
                const dmMessage = settings.welcome_dm_message
                    .replace(/{user}/g, `<@${member.id}>`)
                    .replace(/{username}/g, member.user.username)
                    .replace(/{server}/g, guild.name)
                    .replace(/{membercount}/g, guild.memberCount);

                await member.send(dmMessage).catch(() => { }); // Ignore DM errors (closed DMs)
            }

            console.log(`[Welcome] Processed for ${member.user.tag} in ${guild.name}`);

        } catch (error) {
            console.error('[Welcome Error]', error);
        }
    }
};
