import { GoogleGenerativeAI } from '@google/generative-ai';
import guildService from '../services/guild.service.js';

const genAI = new GoogleGenerativeAI(process.env.GOOGLE_API_KEY);
const model = genAI.getGenerativeModel({ model: "gemini-pro" });

export async function runAutoChat(client, timeLabel) {
    console.log(`[AUTO-CHAT] Running for schedule: ${timeLabel}`);

    const guilds = client.guilds.cache.map(g => g.id);

    for (const guildId of guilds) {
        const settings = await guildService.getSettings(guildId);

        if (!settings || !settings.general_chat_channel_id) continue;

        try {
            const channel = await client.channels.fetch(settings.general_chat_channel_id);
            if (!channel || !channel.isTextBased()) continue;

            // 1. Fetch History chat buat konteks
            const messages = await channel.messages.fetch({ limit: 15 });
            const chatHistory = messages.reverse().map(m => {
                return `${m.author.username}: ${m.content}`;
            }).join('\n');

            // 2. Generate Prompt Super Gaul & Kompleks
            const prompt = `
            ROLE: Kamu adalah 'SantuyBot', sesepuh di server game Discord ini.
            PERSONALITY:
            - Bahasa: Sangat gaul Jakarta selatan/tongkrongan (lu/gue, anjay, bjir, valid, wkwk).
            - Style: Kadang 'dry text' (singkat, padat, cuek) kalau topik receh. Tapi bisa detail dan helpful kalau ada yang nanya soal ERROR game/PC.
            - Mood: Kamu manusiawi. Kalau server sepi banget, kamu bisa ngedumel, bosen, atau marah-marah manja minta ditemenin.
            - Attitude: Friendly tapi kadang toxic playful (kasar dikit boleh buat seru-seruan asal konteks bercanda).

            CONTEXT:
            Sekarang jam: ${timeLabel}.
            
            HISTORY CHAT TERAKHIR:
            """
            ${chatHistory}
            """
            
            INSTRUCTION:
            - Baca history di atas.
            - Kalau history kosong atau pesan terakhir udah lama banget: KOMPLAIN! Marah-marah kenapa sepi, atau ajak main game dengan nada bete.
            - Kalau ada yang nanya soal ERROR/BUG/TECH: Jawab solusinya dengan bahasa gaul tapi teknisnya bener.
            - Kalau lagi obrolan santai: Timpalin sesuka hati, bisa singkat (dry text) atau panjang kalau seru.
            - JANGAN PERNAH MENYAPA "Halo Semuanya" kayak bot kaku. Langsung to the point aja.
            - JANGAN PAKAI FORMAT LIST/BULLET POINTS. Pake gaya chat biasa.
            - Output teks saja, tanpa tanda kutip.
            `;

            // 3. Call Gemini
            const result = await model.generateContent(prompt);
            const response = result.response.text();

            // 4. Send Message
            if (response) {
                await channel.sendTyping();

                // Typing duration based on length (realism)
                const typingDuration = Math.min(response.length * 50, 5000);

                setTimeout(async () => {
                    await channel.send(response);
                }, typingDuration);
            }

        } catch (error) {
            console.error(`[AUTO-CHAT ERROR] Guild ${guildId}:`, error.message);
        }
    }
}
