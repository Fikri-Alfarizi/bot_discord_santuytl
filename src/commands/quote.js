import { SlashCommandBuilder } from 'discord.js';

const quotes = [
    { text: "Jangan pernah menyerah, karena cicilan belum lunas.", author: "Realita" },
    { text: "Hidup itu seperti roda, kadang di atas, kadang di bawah, kadang bannya bocor.", author: "Kang Tambal Ban" },
    { text: "Kerja keraslah sampai tetanggamu mengira kamu pelihara tuyul.", author: "Motivasi Sukses" },
    { text: "Seberat apapun masalahmu, jangan lupa makan. Karena pura-pura bahagia itu butuh tenaga.", author: "Sobat Ambyar" },
    { text: "Jika kamu merasa tidak berguna, ingatlah bahwa ada lampu sein di mobil ibu-ibu.", author: "Fakta Jalanan" },
    { text: "Uang bukan segalanya, tapi segalanya butuh uang.", author: "Hukum Alam" },
    { text: "Masa depan tergantung pada impianmu. Maka pergilah tidur.", author: "Bob Sadino (Mungkin)" },
    { text: "Bermimpilah setinggi langit. Jika engkau jatuh, engkau akan jatuh di antara bintang-bintang... atau di genteng orang.", author: "Soekarno (Remix)" },
    { text: "Gak usah sok keras, kalau hati masih selembut tahu sutra.", author: "Kata-kata Keren" },
    { text: "Sahabat sejati adalah mereka yang tahu betapa gilanya kamu, tapi masih mau berteman denganmu.", author: "Anonim" }
];

export const data = new SlashCommandBuilder()
    .setName('quote')
    .setDescription('Dapet kata-kata mutiara random (biasanya ngawur dikit)');

export async function execute(interaction) {
    const randomQuote = quotes[Math.floor(Math.random() * quotes.length)];

    const embed = {
        color: 0x9B59B6,
        description: `*"${randomQuote.text}"*`,
        footer: { text: `~ ${randomQuote.author}` }
    };

    await interaction.reply({ embeds: [embed] });
}
