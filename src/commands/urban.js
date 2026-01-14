import { SlashCommandBuilder } from 'discord.js';
import axios from 'axios';

export const data = new SlashCommandBuilder()
    .setName('urban')
    .setDescription('Cari definisi kata gaul di Urban Dictionary')
    .addStringOption(option =>
        option.setName('term')
            .setDescription('Kata yang ingin dicari')
            .setRequired(true));

export async function execute(interaction) {
    const term = interaction.options.getString('term');
    await interaction.deferReply();
    try {
        const res = await axios.get(`https://api.urbandictionary.com/v0/define?term=${encodeURIComponent(term)}`);
        const list = res.data.list;

        if (!list || list.length === 0) {
            return await interaction.editReply(`Tidak ditemukan definisi untuk: **${term}**`);
        }

        const def = list[0];
        const embed = {
            title: `📚 Urban Dictionary: ${term}`,
            description: def.definition.replace(/\[|\]/g, ''),
            fields: [
                { name: 'Example', value: def.example.replace(/\[|\]/g, '') || '-' }
            ],
            footer: { text: `👍 ${def.thumbs_up} | 👎 ${def.thumbs_down}` },
            url: def.permalink,
            color: 0xE67E22
        };
        await interaction.editReply({ embeds: [embed] });
    } catch (err) {
        await interaction.editReply('Gagal mengambil definisi.');
    }
}
