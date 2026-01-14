import { SlashCommandBuilder } from 'discord.js';
import { SHOP_ITEMS } from '../economy/shop.items.js';

export const data = new SlashCommandBuilder()
    .setName('shop')
    .setDescription('Lihat barang-barang keren yang bisa dibeli');

export async function execute(interaction) {
    const items = SHOP_ITEMS.map(item => {
        return `📦 **${item.name}**\n💰 Harga: \`RP ${item.price.toLocaleString()}\`\n📝 ${item.description}\n🆔 ID: \`${item.id}\`\n`;
    }).join('\n');

    const embed = {
        title: '🛒 **SANTUY MART**',
        description: '*Belanja barang-barang gaul buat pamer ke temen!* \n\nGunakan `/buy <id>` untuk membeli.\n\n' + items,
        color: 0x00A8FF,
        thumbnail: { url: 'https://media.giphy.com/media/3o7TKSjRrfIPjeiSEQ/giphy.gif' }, // Shopping cart GIF
        footer: { text: 'SantuyTL Economy System' }
    };

    await interaction.reply({ embeds: [embed] });
}
