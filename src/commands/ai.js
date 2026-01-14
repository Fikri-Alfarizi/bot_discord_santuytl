import { SlashCommandBuilder } from 'discord.js';
import { askGemini } from '../services/gemini.service.js';

export const data = new SlashCommandBuilder()
    .setName('ai')
    .setDescription('Ngobrol santuy sama bot (Tanya apa aja!)')
    .addStringOption(option =>
        option.setName('pertanyaan')
            .setDescription('Tanya apa bro?')
            .setRequired(true));

export async function execute(interaction) {
    await interaction.deferReply();
    const query = interaction.options.getString('pertanyaan');

    const response = await askGemini(interaction.user.username, query);

    // Discord limit 2000 chars
    if (response.length > 1900) {
        await interaction.editReply(response.substring(0, 1900) + '... (kepanjangan bjir)');
    } else {
        await interaction.editReply(response);
    }
}
