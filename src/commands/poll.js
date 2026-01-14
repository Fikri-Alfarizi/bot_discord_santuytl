import { SlashCommandBuilder } from 'discord.js';

export const data = new SlashCommandBuilder()
    .setName('poll')
    .setDescription('Buat voting sederhana')
    .addStringOption(option =>
        option.setName('question')
            .setDescription('Pertanyaan voting')
            .setRequired(true));

export async function execute(interaction) {
    const question = interaction.options.getString('question');

    const embed = {
        title: '📊 POLL',
        description: question,
        color: 0xFFFF00,
        footer: { text: `Poll by ${interaction.user.tag}` },
        timestamp: new Date()
    };

    const message = await interaction.reply({ embeds: [embed], fetchReply: true });
    await message.react('👍');
    await message.react('👎');
}
