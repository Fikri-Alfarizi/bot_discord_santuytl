import cron from 'node-cron';
import client from '../config/discord.js';
import { generateDailyTopic } from '../services/ai.service.js';

export function scheduleDailyYap() {
    // Jadwal jam 7 pagi setiap hari (0 7 * * *)
    cron.schedule('0 7 * * *', async () => {
        console.log('Running daily AI yap task...');
        const topic = await generateDailyTopic();

        if (!topic) return;

        const targetChannelIds = [
            process.env.DISCORD_GAME_CHANNEL_ID || '1391274558514004019',
            process.env.DISCORD_EVENT_CHANNEL_ID || '1439538769148772372'
        ];

        const uniqueIds = [...new Set(targetChannelIds)].filter(id => id);

        for (const channelId of uniqueIds) {
            try {
                const channel = await client.channels.fetch(channelId);
                if (channel && channel.isTextBased()) {
                    const embed = {
                        title: '📢 Daily Yap Session! 🗣️',
                        description: topic,
                        color: 0x00FF00,
                        footer: { text: 'Bot SantuyTL - AI Powered' },
                        timestamp: new Date()
                    };
                    await channel.send({ embeds: [embed] });
                }
            } catch (err) {
                console.error(`Failed to send daily yap to ${channelId}:`, err.message);
            }
        }
    }, {
        timezone: "Asia/Jakarta"
    });

    console.log('Daily AI Yap scheduler started (07:00 WIB).');
}
