export const JOBS = [
    {
        id: 'programmer',
        name: '👨‍💻 Programmer',
        description: 'Ngoding bagai kuda, gaji gede tapi stress.',
        salary: { min: 3000, max: 8000 },
        cooldown: 5 * 60 * 1000,
        tasks: [
            { question: 'Apa output dari `console.log(1 + "1")`?', options: ['11', '2', 'Error'], answer: '11' },
            { question: 'Simbol logika AND di JS?', options: ['&&', '||', '&'], answer: '&&' },
            { question: 'Biar codingan rapi pake apa?', options: ['Prettier', 'Word', 'Paint'], answer: 'Prettier' }
        ]
    },
    {
        id: 'ojol',
        name: '🛵 Driver Ojol',
        description: 'Antar jemput paket, kadang dapet tip.',
        salary: { min: 2000, max: 5000 },
        cooldown: 3 * 60 * 1000,
        tasks: [
            { question: 'Customer minta jemput di mana?', options: ['Lobby', 'Atap', 'Kolam'], answer: 'Lobby' },
            { question: 'Lampu merah artinya?', options: ['Berhenti', 'Gaspol', 'Terbang'], answer: 'Berhenti' }
        ]
    },
    {
        id: 'gamer',
        name: '🎮 Pro Gamer',
        description: 'Main game doang dapet duit.',
        salary: { min: 4000, max: 7000 },
        cooldown: 5 * 60 * 1000,
        tasks: [
            { question: 'Pencet apa buat nembak?', options: ['Klik Kiri', 'Space', 'Alt+F4'], answer: 'Klik Kiri' },
            { question: 'Istilah Carry artinya?', options: ['Gendong Tim', 'Beban', 'AFK'], answer: 'Gendong Tim' }
        ]
    },
    {
        id: 'selebgram',
        name: '📸 Selebgram',
        description: 'Endorse barang KW, followers jutaan.',
        salary: { min: 5000, max: 10000 },
        cooldown: 10 * 60 * 1000,
        tasks: [
            { question: 'Kalo foto harus?', options: ['Pake Filter', 'Gelap', 'Blur'], answer: 'Pake Filter' },
            { question: 'Caption yg bagus?', options: ['Link di Bio', 'Jualan', 'Curhat'], answer: 'Link di Bio' }
        ]
    }
];

export function getJobById(id) {
    return JOBS.find(j => j.id === id);
}
