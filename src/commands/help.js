import { SlashCommandBuilder } from 'discord.js';

export const data = new SlashCommandBuilder()
    .setName('help')
    .setDescription('Panduan lengkap semua fitur SantuyTL');

export async function execute(interaction) {
    const embed = {
        title: '📘 SantuyTL System Manual',
        description: 'Selamat datang di **SantuyTL**. Bot ini lebih dari sekadar command, ini adalah sistem ekosistem server ini.\n\n*Pilih kategori di bawah untuk melihat detail.*',
        color: 0x5865F2,
        thumbnail: { url: interaction.client.user.displayAvatarURL() },
        fields: [
            {
                name: '🏆 Season & Rank',
                value: '`/season` Info Season\n`/rep` Give Respect\n`/trust` Trust Score',
                inline: true
            },
            {
                name: '💸 Economy',
                value: '`/job` Cari Cuan\n`/work` Alias Job\n`/daily` Lapor Harian',
                inline: true
            },
            {
                name: '🤖 AI Assistant',
                value: '`/ai` Chat Gemini (v1beta)\n*Reply bot buat lanjut ngobrol.*',
                inline: true
            },
            {
                name: '🎢 Fun & Growth',
                value: '`/spin` Lucky Wheel\n`/invite` Cek Invites\n`/weekly` Gaji Mingguan',
                inline: true
            },
            {
                name: '🛡️ Admin & Utils',
                value: '`/mod` Moderation\n`/settings` Config\n`/ping` Cek Latency',
                inline: false
            },
            {
                name: '📢 Automated Systems',
                value: '• **Passive Income:** 60 koin/menit (Online)\n• **News Feed:** Berita game update tiap 30 menit.\n• **Trust Observer:** Bot auto-kick spammer.',
                inline: false
            }
        ],
        footer: { text: 'SantuyTL System™ • Season 1' }
    };

    return interaction.reply({ embeds: [embed] });
}
