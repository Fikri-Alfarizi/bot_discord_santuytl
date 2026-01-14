import '../config/env.js';
import client from '../config/discord.js';
import axios from 'axios';

async function migrateAllEventsFromChannel() {
    const EVENT_CHANNEL_ID = process.env.DISCORD_EVENT_CHANNEL_ID || '1439538769148772372';
    const API_EVENT_URL = process.env.API_EVENT_URL || process.env.LARAVEL_API_URL + '/api/discord/event';

    const channel = await client.channels.fetch(EVENT_CHANNEL_ID);
    if (!channel || channel.type !== 0) {
        console.error('Channel event tidak ditemukan atau bukan text channel');
        return;
    }
    let lastId = undefined;
    let total = 0;
    while (true) {
        const options = { limit: 100 };
        if (lastId) options.before = lastId;
        const messages = await channel.messages.fetch(options);
        if (messages.size === 0) break;
        for (const message of messages.values()) {
            if (message.author.bot) continue;

            const lines = message.content.split('\n');
            const title = lines[0] || 'Event Baru';
            const date = lines[1] || null;
            const description = lines.slice(2).join('\n');
            let image = null;
            if (message.attachments.size > 0) {
                image = message.attachments.first().url;
            }
            try {
                await axios.post(API_EVENT_URL, {
                    title,
                    date,
                    description,
                    image,
                    discord_message_id: message.id
                });
                total++;
                console.log('Migrated event:', title);
            } catch (err) {
                console.error('Gagal migrasi event:', err.message);
            }
        }
        lastId = messages.last().id;
        if (messages.size < 100) break;
    }
    console.log('Migrasi event selesai. Total:', total);
}

client.once('ready', async () => {
    console.log('Starting migration...');
    await migrateAllEventsFromChannel();
    console.log('Done.');
    process.exit(0);
});

client.login(process.env.DISCORD_BOT_TOKEN);
