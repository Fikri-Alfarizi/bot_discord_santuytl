import { GoogleGenerativeAI } from '@google/generative-ai';

export async function generateDailyTopic() {
    const apiKey = process.env.GEMINI_API_KEY;
    if (!apiKey) {
        console.error('GEMINI_API_KEY not found!');
        return null;
    }

    try {
        const genAI = new GoogleGenerativeAI(apiKey);
        const model = genAI.getGenerativeModel({ model: "gemini-1.5-flash" });

        const prompt = "Buatkan satu topik obrolan yang seru, lucu, atau menarik untuk komunitas Discord gamer dan teknologi. Gunakan bahasa Indonesia gaul yang santai. Ajak member untuk berdiskusi pendek. Jangan terlalu panjang, maksimal 2-3 kalimat.";

        const result = await model.generateContent(prompt);
        const response = await result.response;
        return response.text();
    } catch (error) {
        console.error('Error generating AI topic:', error.message);
        return null;
    }
}
