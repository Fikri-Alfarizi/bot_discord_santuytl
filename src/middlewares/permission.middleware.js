import { PermissionFlagsBits } from 'discord.js';

/**
 * Middleware untuk cek permission Discord
 * @param {Array} permissions - Array of PermissionFlagsBits
 */
export function requirePermissions(...permissions) {
    return async (interaction) => {
        if (!interaction.member) {
            return { allowed: false, reason: '❌ Command ini hanya bisa dipakai di server!' };
        }

        const missingPerms = [];
        for (const perm of permissions) {
            if (!interaction.member.permissions.has(perm)) {
                missingPerms.push(perm);
            }
        }

        if (missingPerms.length > 0) {
            const permNames = missingPerms.map(p => `\`${p}\``).join(', ');
            return {
                allowed: false,
                reason: `❌ **Akses Ditolak!**\nKamu butuh permission: ${permNames}`
            };
        }

        return { allowed: true };
    };
}

/**
 * Shortcut untuk admin only
 */
export const requireAdmin = () => requirePermissions(PermissionFlagsBits.Administrator);

/**
 * Shortcut untuk moderator
 */
export const requireModerator = () => requirePermissions(
    PermissionFlagsBits.KickMembers,
    PermissionFlagsBits.BanMembers
);

/**
 * Shortcut untuk manage messages
 */
export const requireManageMessages = () => requirePermissions(PermissionFlagsBits.ManageMessages);
