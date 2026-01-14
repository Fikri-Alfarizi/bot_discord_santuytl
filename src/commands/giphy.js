import { SlashCommandBuilder } from 'discord.js';
import axios from 'axios';

export const data = new SlashCommandBuilder()
    .setName('giphy')
    .setDescription('Cari GIF dari Giphy')
    .addStringOption(option =>
        option.setName('keyword')
            .setDescription('Kata kunci GIF')
            .setRequired(true));

export async function execute(interaction) {
    const keyword = interaction.options.getString('keyword');
    const apiKey = process.env.GIPHY_API_KEY;

    if (!apiKey) {
        return await interaction.reply({ content: 'Giphy API Key belum dikonfigurasi!', ephemeral: true });
    }

    await interaction.deferReply();

    try {
        const res = await axios.get('https://api.giphy.com/v1/gifs/random', {
            params: {
                api_key: apiKey,
                tag: keyword,
                rating: 'g'
            }
        });

        const gifUrl = res.data.data?.images?.original?.url;

        if (gifUrl) {
            await interaction.editReply(gifUrl);
        } else {
            await interaction.editReply(`Tidak ditemukan GIF untuk: ${keyword}`);
        }

    } catch (error) {
        console.error('Giphy Error:', error.message);
        await interaction.editReply('Gagal mengambil GIF.');
    }
}
