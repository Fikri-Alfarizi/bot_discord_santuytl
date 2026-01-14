import { SlashCommandBuilder } from 'discord.js';

export const data = new SlashCommandBuilder()
    .setName('ping')
    .setDescription('Cek sinyal bot (ngebut atau keong?)');

export async function execute(interaction) {
    const sent = await interaction.reply({ content: '🏓 **PONG!** Mengambil data sinyal...', fetchReply: true });
    const latency = sent.createdTimestamp - interaction.createdTimestamp;
    const wsPing = interaction.client.ws.ping;

    let status = '🚀 Ngebut banget!';
    if (latency > 200) status = '🐢 Lumayan santuy...';
    if (latency > 500) status = '🐌 Lemot parah cuuuy!';

    const embed = {
        color: latency < 200 ? 0x00FF00 : 0xFF0000,
        title: '📶 **CONNECTION STATUS**',
        fields: [
            { name: 'Bot Latency', value: `\`${latency}ms\``, inline: true },
            { name: 'API Latency', value: `\`${wsPing}ms\``, inline: true }
        ],
        footer: { text: status }
    };

    await interaction.editReply({ content: null, embeds: [embed] });
}
