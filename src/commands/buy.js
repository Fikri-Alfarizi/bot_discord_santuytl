import { SlashCommandBuilder } from 'discord.js';
import userService from '../services/user.service.js';
import inventoryService from '../economy/inventory.service.js';
import { getItemById } from '../economy/shop.items.js';
import { logEconomy } from '../utils/auditLogger.js';

export const data = new SlashCommandBuilder()
    .setName('buy')
    .setDescription('Beli barang dari shop')
    .addStringOption(option =>
        option.setName('item_id')
            .setDescription('ID barang yang mau dibeli (Lihat di /shop)')
            .setRequired(true));

export async function execute(interaction) {
    await interaction.deferReply();

    const itemId = interaction.options.getString('item_id');
    const item = getItemById(itemId);
    const userId = interaction.user.id;
    const username = interaction.user.username;

    if (!item) {
        return await interaction.editReply('❌ **Barang gak ketemu!** Cek lagi ID-nya di `/shop`.');
    }

    // Cek duit user
    const user = await userService.getUser(userId, username);
    if (user.coins < item.price) {
        return await interaction.editReply(`❌ **Duit kurang bos!**\nHarga: \`RP ${item.price}\`\nDuitmu: \`RP ${user.coins}\``);
    }

    // Proses transaksi
    try {
        // Kurangi coin
        await userService.addCoins(userId, username, -item.price);

        // Masukin inventory
        // Kalau item ada durasi, hitung expirasinya
        const expiresAt = item.duration ? Math.floor((Date.now() + item.duration) / 1000) : null;

        await inventoryService.addItem(userId, item.id, expiresAt, {
            originalPrice: item.price,
            boughtAt: new Date().toISOString()
        });

        // Log audit
        logEconomy('BUY_ITEM', interaction.user, item.price, `Bought item: ${item.name}`);

        const embed = {
            title: '✅ **TRANSAKSI SUKSES!**',
            description: `Selamat! Kamu berhasil membeli **${item.name}**.\n\nDuit berkurang: \`RP ${item.price.toLocaleString()}\`\nSisa duit: \`RP ${(user.coins - item.price).toLocaleString()}\``,
            color: 0x00FF00,
            thumbnail: { url: 'https://media.giphy.com/media/l0HlOaQcLn2h7uO2I/giphy.gif' }
        };

        await interaction.editReply({ embeds: [embed] });

    } catch (error) {
        console.error(error);
        await interaction.editReply('❌ **Error transaksi!** Duitmu aman kok, coba lagi nanti.');
    }
}
