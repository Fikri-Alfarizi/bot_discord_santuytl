import { SlashCommandBuilder } from 'discord.js';
import userService from '../services/user.service.js';

export const data = new SlashCommandBuilder()
    .setName('afk')
    .setDescription('Set status AFK (Away From Keyboard)')
    .addStringOption(option =>
        option.setName('reason')
            .setDescription('Alasan AFK (Contoh: Tidur, Makan, Berak)')
            .setRequired(false));

export async function execute(interaction) {
    const reason = interaction.options.getString('reason') || 'Tanpa alasan';
    const userId = interaction.user.id;
    const username = interaction.user.username;

    userService.setAfk(userId, username, reason);

    // Ganti nickname kalau bisa (Optional, permission dependent)
    try {
        if (interaction.member.manageable) {
            const oldNick = interaction.member.nickname || interaction.user.username;
            if (!oldNick.includes('[AFK]')) {
                await interaction.member.setNickname(`[AFK] ${oldNick}`);
            }
        }
    } catch (e) {
        // Ignore permission error
    }

    const embed = {
        description: `💤 **${interaction.user.username}** sekarang AFK.\n📝 **Alasan:** ${reason}`,
        color: 0x95A5A6
    };

    await interaction.reply({ embeds: [embed] });
}
