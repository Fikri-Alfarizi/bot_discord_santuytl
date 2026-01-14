import { SlashCommandBuilder } from 'discord.js';
import reputationService from '../services/reputation.service.js';

export const data = new SlashCommandBuilder()
    .setName('rep')
    .setDescription('Sistem Reputasi & Social Credit')
    .addSubcommand(sub =>
        sub.setName('give')
            .setDescription('Kasih +1 Respect ke orang lain')
            .addUserOption(opt => opt.setName('user').setDescription('Target user').setRequired(true)))
    .addSubcommand(sub =>
        sub.setName('stats')
            .setDescription('Cek reputasi user')
            .addUserOption(opt => opt.setName('user').setDescription('Target user')));

export async function execute(interaction) {
    const subcommand = interaction.options.getSubcommand();
    const targetUser = interaction.options.getUser('user') || interaction.user;

    if (subcommand === 'give') {
        if (targetUser.id === interaction.user.id) {
            return interaction.reply({ content: 'Gak bisa kasih rep ke diri sendiri bang jago.', ephemeral: true });
        }

        const result = await reputationService.giveReputation(interaction.user.id, targetUser.id);

        if (result.success) {
            return interaction.reply(`✅ **Respect!** ${interaction.user} ngasih +1 Rep ke ${targetUser}.`);
        } else {
            return interaction.reply({ content: `⏳ ${result.message}`, ephemeral: true });
        }
    }

    if (subcommand === 'stats') {
        const stats = await reputationService.getReputation(targetUser.id);

        return interaction.reply({
            embeds: [{
                title: `🛡️ Reputasi: ${targetUser.username}`,
                description: `🌟 **Total Rep:** ${stats.rep_points}\n\n*Makin tinggi makin "diorangkan" di server ini.*`,
                color: 0x3498DB
            }]
        });
    }
}
