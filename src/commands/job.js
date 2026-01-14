import { SlashCommandBuilder, StringSelectMenuBuilder, ActionRowBuilder, ComponentType } from 'discord.js';
import { JOBS } from '../config/jobs/jobs.data.js';
import { query, get } from '../db/index.js';

export const data = new SlashCommandBuilder()
    .setName('job')
    .setDescription('[👤 Public] Lamar pekerjaan baru');

export async function execute(interaction) {
    const user = await get('SELECT job FROM users WHERE id = ?', [interaction.user.id]);
    const currentJob = user?.job || 'Pengangguran';

    const options = JOBS.map(job => ({
        label: job.name,
        description: job.description,
        value: job.id,
        emoji: job.name.split(' ')[0]
    }));

    const selectMenu = new StringSelectMenuBuilder()
        .setCustomId('job_select')
        .setPlaceholder('Pilih pekerjaan yang cocok...')
        .addOptions(options);

    const row = new ActionRowBuilder().addComponents(selectMenu);

    const embed = {
        title: '🏢 **LOWONGAN KERJA SANTUY**',
        description: `Pekerjaan kamu sekarang: **${currentJob}**\n\nPilih karir baru kamu di bawah ini! Jangan jadi pengangguran terus.`,
        color: 0x3498DB
    };

    const response = await interaction.reply({ embeds: [embed], components: [row] });

    // Collector for interaction
    const collector = response.createMessageComponentCollector({ componentType: ComponentType.StringSelect, time: 60000 });

    collector.on('collect', async i => {
        if (i.user.id !== interaction.user.id) {
            return i.reply({ content: 'Hush! Ini bukan lamaran kamu!', ephemeral: true });
        }

        const selectedJobId = i.values[0];
        const job = JOBS.find(j => j.id === selectedJobId);

        // Store Job ID (cleaner), not name
        await query('UPDATE users SET job = ? WHERE id = ?', [job.id, i.user.id]);

        await i.update({
            content: `🎉 Selamat! Kamu sekarang bekerja sebagai **${job.name}**.\nGunakan \`/work\` untuk mulai cari duit!`,
            embeds: [],
            components: []
        });
    });
}
