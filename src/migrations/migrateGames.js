import '../config/env.js';
import client from '../config/discord.js';
import axios from 'axios';

async function migrateAllGamesFromChannel() {
    const GAME_CHANNEL_ID = process.env.DISCORD_GAME_CHANNEL_ID || '1391274558514004019';
    const API_GAME_URL = process.env.API_GAME_URL || process.env.LARAVEL_API_URL + '/api/discord/game';

    const channel = await client.channels.fetch(GAME_CHANNEL_ID);
    if (!channel || channel.type !== 0) {
        console.error('Channel game tidak ditemukan atau bukan text channel');
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
            const title = lines[0] || 'Game Baru';
            const link = lines.find(l => l.includes('http')) || '';
            const description = lines.filter(l => l !== title && !l.includes('http')).join('\n');
            let image = null;
            if (message.attachments.size > 0) {
                image = message.attachments.first().url;
            }
            try {
                await axios.post(API_GAME_URL, {
                    title,
                    link,
                    description,
                    image,
                    discord_message_id: message.id
                });
                total++;
                console.log('Migrated:', title);
            } catch (err) {
                console.error('Gagal migrasi game:', err.message);
            }
        }
        lastId = messages.last().id;
        if (messages.size < 100) break;
    }
    console.log('Migrasi selesai. Total:', total);
}

client.once('ready', async () => {
    console.log('Starting migration...');
    await migrateAllGamesFromChannel();
    console.log('Done.');
    process.exit(0);
});

client.login(process.env.DISCORD_BOT_TOKEN);
