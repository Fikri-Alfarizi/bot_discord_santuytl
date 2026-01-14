import { SlashCommandBuilder } from 'discord.js';

const jokes = [
    // Jokes Bapak-bapak
    "Kenapa ayam kalau berkokok matanya merem?\nKarena sudah hafal teksnya.",
    "Benda apa yang kalau dibalik jadi rusak?\nKasur rusak.",
    "Bebek apa yang jalannya muter ke kiri terus?\nBebek dikunci stang.",
    "Kenapa nyamuk bunyinya 'nging nging'?\nKarena dia menghisap darah, kalau menghisap duit bunyinya 'kring kring'.",
    "Gendang apa yang nggak bisa dipukul?\nGendang telinga.",

    // Jokes Receh
    "Sapi, sapi apa yang nempel di dinding?\nStiker sapi.",
    "Awan apa yang paling bikin seneng?\nAwanna be with you.",
    "Tiang apa yang enak?\nTiang-tiang minum es teh manis.",
    "Pintu apa yang didorong nggak bakalan bisa kebuka?\nPintu yang ada tulisannya 'GESER'.",
    "Kunci apa yang bisa bikin orang joget?\nKunci-kunci hota hee~",

    // Tebak-tebakan
    "Makan apa yang gak boleh dimakan?\nMakan ati.",
    "Kenapa pohon kelapa di depan rumah harus ditebang?\nSoalnya kalau dicabut berat.",
    "Hewan apa yang paling hening?\nSemut lagi mengheningkan cipta.",
    "Apa bedanya soto sama coto?\nKalau soto pake daging sapi, kalau coto pake daging c-api (sapi).",
    "Kenapa superman sempaknya merah?\nKarena lupa pake pemutih pas nyuci."
];

export const data = new SlashCommandBuilder()
    .setName('joke')
    .setDescription('Dapet lawakan random ala tongkrongan');

export async function execute(interaction) {
    const randomJoke = jokes[Math.floor(Math.random() * jokes.length)];
    const [setup, punchline] = randomJoke.split('\n');

    const embed = {
        color: 0xFFD700,
        title: '🤣 **JOKES RECEH**',
        description: `**${setup}**\n\n||${punchline || ''}||`, // Spoiler for punchline if exists
        footer: { text: 'Awas garing, sedia air putih.' }
    };

    await interaction.reply({ embeds: [embed] });
}
