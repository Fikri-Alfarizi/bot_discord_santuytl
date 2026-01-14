import { SlashCommandBuilder } from 'discord.js';
import inventoryService from '../economy/inventory.service.js';
import { getItemById } from '../economy/shop.items.js';
import db from '../db/index.js'; // Direct DB for complex updates if needed (e.g. XP boost state)

export const data = new SlashCommandBuilder()
    .setName('use')
    .setDescription('Pakai item dari inventory')
    .addStringOption(option =>
        option.setName('item_id')
            .setDescription('ID barang yang mau dipakai')
            .setRequired(true));

export async function execute(interaction) {
    await interaction.deferReply();

    const itemId = interaction.options.getString('item_id');
    const userId = interaction.user.id;

    if (!inventoryService.hasItem(userId, itemId)) {
        return await interaction.editReply('❌ **Kamu gak punya barang ini!** Cek `/inventory` dulu.');
    }

    const itemDef = getItemById(itemId);
    if (!itemDef) {
        return await interaction.editReply('❌ **Item tidak dikenali sistem.**');
    }

    try {
        // Logic pemakaian item
        let successMessage = '';

        switch (itemDef.type) {
            case 'role':
                if (itemDef.roleId) {
                    const member = await interaction.guild.members.fetch(userId);
                    await member.roles.add(itemDef.roleId);
                    successMessage = `🎉 **Role Aktif!** Kamu sekarang punya role <@&${itemDef.roleId}> selama 7 hari.`;
                } else {
                    successMessage = '⚠️ Config role ID belum diset di `shop.items.js`. Hubungi admin.';
                }
                break;

            case 'rename_bot':
                // Logic rename bot (Requires permission)
                successMessage = '⚙️ Fitur rename bot akan aktif! (Simulasi: Bot name changed)';
                // In real app: await interaction.guild.members.me.setNickname(...)
                break;

            case 'xp_boost':
                // Need a table for active_boosts, for now just consume
                successMessage = '⚡ **XP Boost Aktif!** (Simulasi: XP gaining rate doubled)';
                break;

            default:
                successMessage = `✅ **${itemDef.name}** berhasil dipakai!`;
                break;
        }

        // Konsumsi item (hapus dari inventory)
        // Note: For timed items like Roles, we usually KEEP the record but check 'expires_at'. 
        // If 'expires_at' was set during BUY, then we don't need to "consume" it here unless it's a consumable potion.
        // BUT, looking at buy logic, we set expiresAt.
        // IF the item is "Activate to use" (like potion), we consume it.
        // IF the item is "Passive duration" (like role), it automtaically activates on buy (usually).
        // Let's assume /use is for consumable one-time effects or creating the effect.

        // For simplicity: We consume "consumables", but "duration" items might just show status.
        // Let's assume we consume one stack of the item.
        inventoryService.useItem(userId, itemId);

        await interaction.editReply(successMessage);

    } catch (error) {
        console.error(error);
        await interaction.editReply('❌ **Gagal pakai item!** Ada masalah teknis.');
    }
}
