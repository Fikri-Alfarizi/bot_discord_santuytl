import { SlashCommandBuilder } from 'discord.js';
import inventoryService from '../economy/inventory.service.js';
import { getItemById } from '../economy/shop.items.js';

export const data = new SlashCommandBuilder()
    .setName('inventory')
    .setDescription('Cek tas inventaris kamu');

export async function execute(interaction) {
    await interaction.deferReply();

    const items = await inventoryService.getUserInventory(interaction.user.id);

    if (items.length === 0) {
        return await interaction.editReply('🎒 **Tas kamu kosong melompong!** Belanja dulu gih di `/shop`.');
    }

    const inventoryList = items.map((invItem, i) => {
        const itemDef = getItemById(invItem.item_id);
        const name = itemDef ? itemDef.name : `Unknown Item (${invItem.item_id})`;
        const expiry = invItem.expires_at
            ? `\n⏳ Expired: <t:${invItem.expires_at}:R>`
            : '\n♾️ Permanen';

        return `**${i + 1}. ${name}**${expiry}`;
    }).join('\n\n');

    const embed = {
        title: `🎒 **INVENTORY ${interaction.user.username.toUpperCase()}**`,
        description: inventoryList,
        color: 0xF1C40F,
        footer: { text: 'Gunakan /use <item_id> untuk pakai item' }
    };

    await interaction.editReply({ embeds: [embed] });
}
