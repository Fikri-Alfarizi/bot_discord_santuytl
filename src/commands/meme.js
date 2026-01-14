import { SlashCommandBuilder } from 'discord.js';
import { searchMeme } from '../services/meme.service.js';

export const data = new SlashCommandBuilder()
    .setName('meme')
    .setDescription('Cari dan kirim meme/stiker dari internet')
    .addStringOption(option =>
        option.setName('query')
            .setDescription('Kata kunci pencarian meme')
            .setRequired(true))
    .addIntegerOption(option =>
        option.setName('count')
            .setDescription('Jumlah gambar yang dikirim (Stacking) - Max 5')
            .setMinValue(1)
            .setMaxValue(5));

export async function execute(interaction) {
    const query = interaction.options.getString('query');
    const count = interaction.options.getInteger('count') || 1;

    await interaction.deferReply();

    try {
        const images = await searchMeme(query);

        if (images.length === 0) {
            return await interaction.editReply(`Maaf, tidak ditemukan meme untuk pencarian: "${query}"`);
        }

        const selectedImages = images.slice(0, Math.min(count, images.length));
        const embeds = selectedImages.map((img) => ({
            image: { url: img }
        }));

        await interaction.editReply({ embeds: embeds });
    } catch (error) {
        console.error(error);
        await interaction.editReply('Terjadi kesalahan saat mencari meme.');
    }
}
