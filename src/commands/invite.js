import { SlashCommandBuilder } from 'discord.js';
import inviteService from '../services/invite.service.js';

export const data = new SlashCommandBuilder()
    .setName('invite')
    .setDescription('Cek statistik invite kamu')
    .addSubcommand(sub =>
        sub.setName('stats')
            .setDescription('Lihat berapa orang yang udah lo ajak'));

export async function execute(interaction) {
    const count = await inviteService.getInviteCount(interaction.user.id);

    return interaction.reply({
        embeds: [{
            title: `📩 Invite Stats: ${interaction.user.username}`,
            description: `Kamu sudah mengajak **${count}** orang ke server ini.\n\n*Terus ajak temen biar server makin rame!*`,
            color: 0x9B59B6
        }]
    });
}
