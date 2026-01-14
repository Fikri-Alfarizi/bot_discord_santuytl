import './config/env.js';
import express from 'express';
import client from './config/discord.js';
import apiRoutes from './api/index.js';
import { loadCommands } from './commands/index.js';
import { loadEvents } from './events/index.js';
import cron from 'node-cron';
import { checkAndPostNews } from './services/news.service.js';
import { distributePassiveIncome } from './cron/passiveIncome.js';
import { startPresence } from './utils/presence.js';
import { runAutoChat } from './services/autochat.service.js';

const app = express();
const PORT = process.env.PORT || 3001;

// Middleware
app.use(express.json());

// CORS
app.use((req, res, next) => {
    res.header('Access-Control-Allow-Origin', '*');
    res.header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
    res.header('Access-Control-Allow-Headers', 'Content-Type, Authorization');
    if (req.method === 'OPTIONS') return res.sendStatus(200);
    next();
});

// Routes
app.use(express.static('public')); // Serve static files (Terms & Privacy)
app.use('/api', apiRoutes);

// Health Check
app.get('/', (req, res) => {
    res.send('SantuyTL Bot API is running!');
});

// Start Express Server
app.listen(PORT, () => {
    console.log(`HTTP API running on port ${PORT}`);
});

client.once('ready', async () => {
    console.log(`🔥 Bot is ready! Logged in as ${client.user.tag}`);

    // Start Rotating Presence
    startPresence(client);

    // Schedule News Feed (Every 30 minutes)
    cron.schedule('*/30 * * * *', () => {
        checkAndPostNews(client);
    });

    // Schedule Passive Income (Every 1 minute)
    cron.schedule('* * * * *', () => {
        distributePassiveIncome(client);
    });

    // --- GEMINI AUTO CHAT SCHEDULES ---
    // Jam 07:00 Pagi
    cron.schedule('0 7 * * *', () => runAutoChat(client, 'Pagi'));
    // Jam 12:00 Siang
    cron.schedule('0 12 * * *', () => runAutoChat(client, 'Siang'));
    // Jam 15:00 Sore
    cron.schedule('0 15 * * *', () => runAutoChat(client, 'Sore'));
    // Jam 21:00 Malam
    cron.schedule('0 21 * * *', () => runAutoChat(client, 'Malam'));

    console.log('📰 News Feed System: ACTIVE');
    console.log('💰 Passive Income System: ACTIVE (60 coins/min ≈ 1 RP/sec for online users)');
    console.log('🤖 Gemini Auto-Chat: ACTIVE (07:00, 12:00, 15:00, 21:00)');
});

// Initialize Discord Bot
(async () => {
    // Init Database first
    const { initDatabase } = await import('./db/index.js');
    await initDatabase();

    await loadCommands(client);
    await loadEvents(client);
    await client.login(process.env.DISCORD_BOT_TOKEN);
})();
