/**
 * Middleware untuk cek role berdasarkan nama atau ID
 * @param {Array} roleNamesOrIds - Array of role names or IDs
 */
export function requireRoles(...roleNamesOrIds) {
    return async (interaction) => {
        if (!interaction.member) {
            return { allowed: false, reason: '❌ Command ini hanya bisa dipakai di server!' };
        }

        const memberRoles = interaction.member.roles.cache;
        const hasRole = roleNamesOrIds.some(roleIdentifier => {
            return memberRoles.some(role =>
                role.name.toLowerCase() === roleIdentifier.toLowerCase() ||
                role.id === roleIdentifier
            );
        });

        if (!hasRole) {
            const roleList = roleNamesOrIds.map(r => `\`${r}\``).join(', ');
            return {
                allowed: false,
                reason: `❌ **Akses Ditolak!**\nKamu butuh salah satu role: ${roleList}`
            };
        }

        return { allowed: true };
    };
}

/**
 * Check if user is server owner
 */
export function requireOwner() {
    return async (interaction) => {
        if (!interaction.guild) {
            return { allowed: false, reason: '❌ Command ini hanya bisa dipakai di server!' };
        }

        if (interaction.user.id !== interaction.guild.ownerId) {
            return {
                allowed: false,
                reason: '❌ **Akses Ditolak!**\nHanya owner server yang bisa pakai command ini!'
            };
        }

        return { allowed: true };
    };
}
