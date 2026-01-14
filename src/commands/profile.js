import { SlashCommandBuilder } from 'discord.js';
import userService from '../services/user.service.js';

export const data = new SlashCommandBuilder()
    .setName('profile')
    .setDescription('Cek statistik dan level kepangkatan kamu')
    .addUserOption(option =>
        option.setName('user')
            .setDescription('Mau kepoin siapa? (Kosongin buat cek diri sendiri)'));

export async function execute(interaction) {
    await interaction.deferReply();

    const targetUser = interaction.options.getUser('user') || interaction.user;

    // Get stats
    const userData = userService.getUser(targetUser.id, targetUser.username);
    const member = await interaction.guild.members.fetch(targetUser.id);

    // XP Logic
    const xpNeeded = userData.level * 100;
    const progress = Math.floor((userData.xp / xpNeeded) * 100);

    // Visual Progress Bar
    const barLength = 12;
    const filled = Math.floor(progress / (100 / barLength));
    const empty = barLength - filled;
    const progressBar = '▰'.repeat(filled) + '▱'.repeat(empty);

    const embed = {
        title: `👤 **PROFIL MEMBER: ${targetUser.username.toUpperCase()}**`,
        description: `Berikut adalah statistik pencapaian kamu di server **${interaction.guild.name}**. Terus aktif biar makin sepuh! 🚀`,
        thumbnail: { url: targetUser.displayAvatarURL({ dynamic: true, size: 512 }) },
        color: member.displayColor !== 0 ? member.displayColor : 0xFFD700, // Role color or Gold
        image: {
            url: 'https://placehold.co/600x100/2f3136/ACACAC.png?text=LEVEL+UP+YOUR+GAME&font=Montserrat' // Aesthetic spacer/banner optional
        },
        fields: [
            {
                name: '🏅 **LEVEL SAAT INI**',
                value: `\`\`\`css\n[ ${userData.level} ]\`\`\``,
                inline: true
            },
            {
                name: '💰 **KEKAYAAN (COINS)**',
                value: `\`\`\`yaml\nRP ${userData.coins.toLocaleString()}\`\`\``,
                inline: true
            },
            {
                name: '✨ **PROGRESS NEXT LEVEL**',
                value: `> **${userData.xp}** / ${xpNeeded} XP\n> \`[${progressBar}] ${progress}%\``,
                inline: false
            },
            {
                name: '📅 **JOIN SEJAK**',
                value: `> <t:${Math.floor(member.joinedTimestamp / 1000)}:D> (<t:${Math.floor(member.joinedTimestamp / 1000)}:R>)`,
                inline: false
            }
        ],
        footer: { text: `ID: ${targetUser.id} • SantuyTL Economy`, icon_url: interaction.guild.iconURL() }
    };

    await interaction.editReply({ embeds: [embed] });
}
