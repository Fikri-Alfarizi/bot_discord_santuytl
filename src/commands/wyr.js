import { SlashCommandBuilder } from 'discord.js';

const wyrQuestions = [
    "Pilih mana: Bisa terbang tapi cuma setinggi 1 meter, atau bisa teleportasi tapi cooldown 10 jam?",
    "Pilih mana: Selalu tahu kalau ada orang bohong, atau selalu tahu kalau ada orang naksir kamu?",
    "Pilih mana: Hidup tanpa musik, atau hidup tanpa internet (selain untuk kerja)?",
    "Pilih mana: Punya uang tak terbatas tapi sendirian, atau hidup pas-pasan bareng teman-teman terbaik?",
    "Pilih mana: Makan nasi goreng tiap hari seumur hidup, atau makan mie instan tiap hari seumur hidup?",
    "Pilih mana: Terjebak di pulau terpencil sama mantan, atau sama musuh bebuyutan?",
    "Pilih mana: Bisa ngomong sama hewan, atau bisa ngomong semua bahasa manusia?",
    "Pilih mana: Jadi orang terpintar di dunia tapi jelek, atau jadi orang terbodoh tapi paling cakep?",
    "Pilih mana: Gak pernah kena macet lagi, atau gak pernah kehabisan baterai HP lagi?",
    "Pilih mana: Makan makanan yang rasanya kayak sampah tapi sehat, atau makan sampah yang rasanya enak banget?"
];

export const data = new SlashCommandBuilder()
    .setName('wyr')
    .setDescription('Mending Mana? Main tebak-tebakan pilihan sulit');

export async function execute(interaction) {
    const randomQuestion = wyrQuestions[Math.floor(Math.random() * wyrQuestions.length)];

    const embed = {
        title: '🤔 **MENDING MANA? (WOULD YOU RATHER)**',
        description: `> ${randomQuestion}`,
        color: 0xFFA500, // Orange
        footer: { text: 'Pikir baik-baik sebelum jawab!' }
    };

    const sent = await interaction.reply({ embeds: [embed], fetchReply: true });
    // Add reactions for voting
    await sent.react('1️⃣');
    await sent.react('2️⃣');
}
