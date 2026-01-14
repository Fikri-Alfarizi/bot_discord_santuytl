import { SlashCommandBuilder } from 'discord.js';
import userService from '../services/user.service.js';

export const data = new SlashCommandBuilder()
    .setName('daily')
    .setDescription('[👤 Public] Klaim gaji harian');

export async function execute(interaction) {
    const userId = interaction.user.id;
    const username = interaction.user.username;

    const REWARD_AMOUNT = 2000; // Bisa diadjust

    const status = await userService.checkDaily(userId);

    if (status.available) {
        await userService.claimDaily(userId, username, REWARD_AMOUNT);

        const embed = {
            title: '💸 **GAJIAN HARIAN CAIR!**',
            description: `Kamu dapet **RP ${REWARD_AMOUNT.toLocaleString()}**!\nJangan lupa balik lagi besok ya!`,
            color: 0x00FF00,
            thumbnail: { url: 'https://media.giphy.com/media/l0Ex6kAKAoFRsFh6M/giphy.gif' }
        };
        await interaction.reply({ embeds: [embed] });
    } else {
        const remainingSeconds = Math.ceil(status.remaining / 1000);
        const hours = Math.floor(remainingSeconds / 3600);
        const minutes = Math.floor((remainingSeconds % 3600) / 60);

        await interaction.reply({
            content: `⏳ **Sabar bos!** Kamu baru bisa ambil gaji lagi dalam **${hours} jam ${minutes} menit**.`,
            ephemeral: true
        });
    }
}
