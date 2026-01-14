import { SlashCommandBuilder } from 'discord.js';
import guildService from '../services/guild.service.js';

export const data = new SlashCommandBuilder()
    .setName('request')
    .setDescription('Request game premium ke admin')
    .addStringOption(option =>
        option.setName('game')
            .setDescription('Nama game yang mau direquest')
            .setRequired(true));

export async function execute(interaction) {
    const gameName = interaction.options.getString('game');
    const guildId = interaction.guild.id;
    const settings = guildService.getSettings(guildId);

    // Cek setting channel
    if (!settings.request_channel_id) {
        return await interaction.reply({
            content: '❌ **Admin belum setup channel request!**\nMinta admin ketik `/settings request <channel>` dulu.',
            ephemeral: true
        });
    }

    try {
        const channel = await interaction.client.channels.fetch(settings.request_channel_id);
        if (!channel) throw new Error('Channel not found');

        // PREMIUM CARD UI
        const embed = {
            author: {
                name: `REQUEST DARI ${interaction.user.username.toUpperCase()}`,
                icon_url: interaction.user.displayAvatarURL({ dynamic: true })
            },
            title: '🎮 NEW GAME REQUEST',
            description: `\nUser ini lagi nyari game:\n# **${gameName}**\n\n*Bantu cariin dong min!*`,
            thumbnail: { url: 'https://media.giphy.com/media/l4pTfx2qLszoacZRS/giphy.gif' }, // Searching GIF
            fields: [
                { name: '📅 Tanggal Request', value: `<t:${Math.floor(Date.now() / 1000)}:F>`, inline: true },
                { name: '👤 Requester', value: interaction.user.toString(), inline: true }
            ],
            color: 0xE74C3C, // Alizarin Red
            footer: { text: 'SantuyTL Request System' }
        };

        const msg = await channel.send({ embeds: [embed] });
        await msg.react('✅'); // Admin bisa centang kalau done
        await msg.react('👀'); // Admin sedang mencari

        // Reply ke user
        await interaction.reply({
            content: `✅ **Request Terkirim!**\nGame **${gameName}** udah masuk daftar antrian request. Tunggu update dari admin ya!`,
            ephemeral: true
        });

    } catch (err) {
        console.error(err);
        await interaction.reply({ content: '❌ **Gagal mengirim request.** Pastikan bot punya akses ke channel request.', ephemeral: true });
    }
}
