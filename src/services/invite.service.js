import { Collection } from 'discord.js';

class InviteService {
    constructor() {
        this.invites = new Collection(); // { guildId: Collection<code, uses> }
    }

    /**
     * Cache invites for a guild
     * @param {Guild} guild 
     */
    async cacheInvites(guild) {
        try {
            const firstInvites = await guild.invites.fetch();
            const inviteUses = new Collection();

            firstInvites.each(invite => inviteUses.set(invite.code, invite.uses));

            this.invites.set(guild.id, inviteUses);
            console.log(`[InviteService] Cached ${firstInvites.size} invites for ${guild.name}`);
        } catch (error) {
            console.error(`[InviteService] Failed to cache invites for ${guild.name}:`, error.message);
        }
    }

    /**
     * Find which invite was used
     * @param {Guild} guild 
     * @returns {Invite|null}
     */
    async findUsedInvite(guild) {
        try {
            const cachedInvites = this.invites.get(guild.id);
            const newInvites = await guild.invites.fetch();

            if (!cachedInvites) {
                // Initialize if missing
                await this.cacheInvites(guild);
                return null;
            }

            let usedInvite = null;

            // Find the invite where count incremented
            for (const [code, invite] of newInvites) {
                const cachedUses = cachedInvites.get(code) || 0;
                if (invite.uses > cachedUses) {
                    usedInvite = invite;
                    break;
                }
            }

            // Update cache
            this.cacheInvites(guild);

            return usedInvite;

        } catch (error) {
            console.error('[InviteService] Error finding used invite:', error);
            return null;
        }
    }
}

export default new InviteService();
