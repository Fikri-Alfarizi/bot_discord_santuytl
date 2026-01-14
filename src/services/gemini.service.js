import axios from 'axios';

export const askGemini = async (username, query, history = []) => {
    try {
        const apiKey = process.env.GOOGLE_API_KEY || process.env.GEMINI_API_KEY;
        if (!apiKey) return "Waduh, API Key gue ilang bro. Bilang admin suruh benerin env variable yak! (Missing API Key)";

        // Use v1beta API
        // Try 'gemini-1.5-flash' first. If this fails, we might need 'gemini-1.5-flash-latest'
        const MODEL = "gemini-2.5-flash";

        // Use standard URL construction
        const API_URL = `https://generativelanguage.googleapis.com/v1beta/models/${MODEL}:generateContent`;

        const prompt = `
        ROLE: Kamu adalah 'SantuyBot', teman ngobrol yang asik, gaul, dan pinter di server Discord ini.
        USER: ${username} bertanya: "${query}"
        
        INSTRUCTION:
        - Jawab dengan bahasa Indonesia gaul (lo/gue, anjay, bjir).
        - Jangan kaku, jangan formal. Anggap aja lagi chat sama temen tongkrongan.
        - Kalau pertanyaannya teknis/coding, kasih jawaban yang bener dan helpful tapi tetep santuy.
        - Kalau pertanyaannya aneh/becanda, timpalin balik dengan lucu atau sarkas dikit.
        - Pendek aja, jangan bikin cerpen, kecuali emang perlu penjelasan panjang.
        - JANGAN pakai format [Role]: [Message]. Langsung jawab aja.
        `;

        const payload = {
            contents: [{
                parts: [{ text: prompt }]
            }],
            generationConfig: {
                maxOutputTokens: 800,
                temperature: 0.9
            }
        };

        const response = await axios.post(API_URL, payload, {
            headers: {
                'Content-Type': 'application/json',
                'x-goog-api-key': apiKey
            },
            timeout: 10000
        });

        if (response.data && response.data.candidates && response.data.candidates.length > 0) {
            const reply = response.data.candidates[0].content.parts[0].text;
            return reply;
        }

        return "Gemini diam seribu bahasa... (No candidates returned)";

    } catch (error) {
        console.error('⚠️ Gemini API Error:', error.response ? error.response.data : error.message);

        const errMsg = error.response ? JSON.stringify(error.response.data) : error.message;

        if (errMsg.includes('429') || errMsg.includes('RESOURCE_EXHAUSTED')) {
            return "Waduh, kebanyakan mikir nih gue (Quota Limit). Tunggu bentar yak! ⏳";
        }
        if (errMsg.includes('SAFETY')) {
            return "Eits, pertanyaan lu terlalu bahaya buat gue jawab wkwk. Skip ah! 🚫";
        }
        if (errMsg.includes('404')) {
            return "Modelnya gak ketemu bro (404). Mungkin salah versi API. 🤕";
        }

        return `Aduh, otak gue lagi korslet. Error: ${error.message} 🤕`;
    }
};
