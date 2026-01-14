import { SlashCommandBuilder } from 'discord.js';
import userService from '../services/user.service.js';

export const data = new SlashCommandBuilder()
    .setName('weekly')
    .setDescription('[👤 Public] Klaim gaji mingguan');

export async function execute(interaction) {
    const userId = interaction.user.id;
    const username = interaction.user.username;

    const REWARD_AMOUNT = 10000; // Bonus gede

    const status = await userService.checkWeekly(userId);

    if (status.available) {
        await userService.claimWeekly(userId, username, REWARD_AMOUNT);

        const embed = {
            title: '🎁 **BONUS MINGGUAN CAIR!**',
            description: `Gokil! Kamu dapet bonus **RP ${REWARD_AMOUNT.toLocaleString()}**!\nTerima kasih udah setia di server ini!`,
            color: 0xFFD700, // Gold
            thumbnail: { url: 'https://media.giphy.com/media/maJfaPl0JNswFJDLPH/giphy.gif' }
        };
        await interaction.reply({ embeds: [embed] });
    } else {
        const remainingSeconds = Math.ceil(status.remaining / 1000);
        const days = Math.floor(remainingSeconds / (3600 * 24));
        const hours = Math.floor((remainingSeconds % (3600 * 24)) / 3600);

        await interaction.reply({
            content: `⏳ **Belum waktunya!** Bonus mingguan bisa diambil lagi dalam **${days} hari ${hours} jam**.`,
            ephemeral: true
        });
    }
}
