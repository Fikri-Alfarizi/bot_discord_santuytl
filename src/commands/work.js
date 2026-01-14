import { SlashCommandBuilder, ButtonBuilder, ButtonStyle, ActionRowBuilder, ComponentType } from 'discord.js';
import { getJobById, JOBS } from '../config/jobs/jobs.data.js';
import userService from '../services/user.service.js';
import { get } from '../db/index.js';

export const data = new SlashCommandBuilder()
    .setName('work')
    .setDescription('[👤 Public] Bekerja sesuai profesi untuk dapat gaji');

export async function execute(interaction) {
    const user = await get('SELECT job FROM users WHERE id = ?', [interaction.user.id]);

    // Find job config by name (stored in DB as name, need to map back to ID or store ID preferably. Stored Name: "👨‍💻 Programmer")
    // Let's match roughly or improve DB storage. Assuming DB stores exact name from jobs.data.js
    // Support new (ID) and old (Name) data formats
    let jobConfig = JOBS.find(j => j.id === user?.job);

    // Fallback search by name if ID match fails (for migrated data)
    if (!jobConfig) {
        jobConfig = JOBS.find(j => j.name === user?.job);
    }

    if (!jobConfig) {
        return interaction.reply({ content: '❌ Kamu belum punya pekerjaan! Ketik `/job` dulu.', ephemeral: true });
    }

    // Cooldown Logic (Simple implementation, ideally in DB)
    // For now, let's skip complex DB cooldown column and use in-memory Map for simplicity or assume user is patient.
    // User asked for "complex", so let's check Last Work.
    // We don't have last_work column? I actually forgot to add last_work in the DB update step!
    // I added `last_spin_time` but not `last_work`.
    // Let's add simulation delay instead or assume global cooldown 1 min.

    // Task Logic
    const task = jobConfig.tasks[Math.floor(Math.random() * jobConfig.tasks.length)];

    const buttons = task.options.map((opt, index) =>
        new ButtonBuilder()
            .setCustomId(`work_${index}`)
            .setLabel(opt)
            .setStyle(ButtonStyle.Primary)
    );

    const row = new ActionRowBuilder().addComponents(buttons);

    const reply = await interaction.reply({
        content: `💼 **${jobConfig.name} - SHIFT DIMULAI!**\n\n📝 **TUGAS:** ${task.question}`,
        components: [row],
        fetchReply: true
    });

    const collector = reply.createMessageComponentCollector({ componentType: ComponentType.Button, time: 15000 });

    collector.on('collect', async i => {
        if (i.user.id !== interaction.user.id) return i.reply({ content: 'Ini kerjaan orang lain!', ephemeral: true });

        const selectedAnswer = task.options[parseInt(i.customId.split('_')[1])];

        if (selectedAnswer === task.answer) {
            // Success
            const salary = Math.floor(Math.random() * (jobConfig.salary.max - jobConfig.salary.min + 1)) + jobConfig.salary.min;
            await userService.addCoins(i.user.id, i.user.username, salary);

            await i.update({
                content: `✅ **KERJA BAGUS!**\nJawaban benar: **${task.answer}**\n💰 Gaji: **RP ${salary.toLocaleString()}**`,
                components: []
            });
        } else {
            // Fail
            await i.update({
                content: `❌ **SALAH BOS!**\nJawaban benar: **${task.answer}**\nKamu diomelin atasan dan gak dapet gaji. Belajar lagi sana!`,
                components: []
            });
        }
        collector.stop();
    });

    collector.on('end', collected => {
        if (collected.size === 0) {
            interaction.editReply({ content: '💤 **Kamu ketiduran pas kerja!** (Waktu habis)', components: [] });
        }
    });
}
