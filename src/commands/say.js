import { SlashCommandBuilder } from 'discord.js';

export const data = new SlashCommandBuilder()
    .setName('say')
    .setDescription('Replies with your input!')
    .addStringOption(option =>
        option.setName('input')
            .setDescription('The input to echo back')
            .setRequired(true));

export async function execute(interaction) {
    const input = interaction.options.getString('input');
    await interaction.reply(input);
}
