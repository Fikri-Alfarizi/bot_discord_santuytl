import { SlashCommandBuilder, PermissionFlagsBits, ActionRowBuilder, ButtonBuilder, ButtonStyle } from 'discord.js';

export const data = new SlashCommandBuilder()
    .setName('setupverify')
    .setDescription('[👑 Admin] Buat panel verifikasi role otomatis')
    .setDefaultMemberPermissions(PermissionFlagsBits.Administrator)
    .addRoleOption(option =>
        option.setName('role')
            .setDescription('Role yang akan diberikan saat klik tombol')
            .setRequired(true))
    .addChannelOption(option =>
        option.setName('channel')
            .setDescription('Channel tempat mengirim panel verifikasi')
            .setRequired(true))
    .addStringOption(option =>
        option.setName('title')
            .setDescription('Judul Panel (Opsional)'))
    .addStringOption(option =>
        option.setName('image')
            .setDescription('URL Gambar/GIF Header (Opsional)'));

export async function execute(interaction) {
    const role = interaction.options.getRole('role');
    const channel = interaction.options.getChannel('channel');
    const title = interaction.options.getString('title') || 'VERIFIKASI MEMBER';
    const image = interaction.options.getString('image') || 'https://media.giphy.com/media/l41lZxzroU33typuU/giphy.gif';

    // Cek Permission Bot
    if (!channel.permissionsFor(interaction.client.user).has(PermissionFlagsBits.SendMessages)) {
        return interaction.reply({ content: '❌ Bot tidak punya izin kirim pesan di channel itu!', ephemeral: true });
    }

    const embed = {
        title: `🛡️ **${title.toUpperCase()}**`,
        description: `Halo selamat datang di **${interaction.guild.name}**!\n\nUntuk mendapatkan akses penuh dan role **${role.name}**, silakan klik tombol verifikasi di bawah ini.\n\n*Click the button below to verify yourself.*`,
        color: 0x2ECC71, // Green
        image: { url: image },
        footer: { text: 'SantuyTL Security System' }
    };

    // Embed Role ID into Button Custom ID: verify_btn_ROLEID
    const button = new ButtonBuilder()
        .setCustomId(`verify_btn_${role.id}`)
        .setLabel(`Verifikasi sebagai ${role.name}`)
        .setStyle(ButtonStyle.Success)
        .setEmoji('✅');

    const row = new ActionRowBuilder().addComponents(button);

    await channel.send({ embeds: [embed], components: [row] });

    await interaction.reply({
        content: `✅ Panel verifikasi berhasil dikirim ke ${channel} untuk role **${role.name}**!`,
        ephemeral: true
    });
}
