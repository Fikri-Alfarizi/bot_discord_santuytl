import fs from 'fs';
import path from 'path';
import { fileURLToPath } from 'url';

const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);

const LOG_DIR = path.join(__dirname, '../../logs');

// Ensure log directory exists
if (!fs.existsSync(LOG_DIR)) {
    fs.mkdirSync(LOG_DIR, { recursive: true });
}

/**
 * Write log to file
 */
function writeLog(filename, message) {
    const timestamp = new Date().toISOString();
    const logMessage = `[${timestamp}] ${message}\n`;
    const logPath = path.join(LOG_DIR, filename);

    fs.appendFileSync(logPath, logMessage, 'utf8');
}

/**
 * Log moderation actions
 */
export function logModeration(action, moderator, target, reason = 'No reason') {
    const message = `ACTION: ${action} | MOD: ${moderator.tag} (${moderator.id}) | TARGET: ${target.tag || target.id} | REASON: ${reason}`;
    writeLog('moderation.log', message);
    console.log(`[MODERATION] ${message}`);
}

/**
 * Log economy transactions
 */
export function logEconomy(action, user, amount, details = '') {
    const message = `ACTION: ${action} | USER: ${user.tag || user.id} | AMOUNT: ${amount} | DETAILS: ${details}`;
    writeLog('economy.log', message);
    console.log(`[ECONOMY] ${message}`);
}

/**
 * Log system events
 */
export function logSystem(event, details = '') {
    const message = `EVENT: ${event} | DETAILS: ${details}`;
    writeLog('system.log', message);
    console.log(`[SYSTEM] ${message}`);
}

/**
 * Send log to Discord channel
 */
export async function sendLogToDiscord(client, logType, embed) {
    const LOG_CHANNEL_ID = process.env.BOT_LOG_CHANNEL_ID;
    if (!LOG_CHANNEL_ID) return;

    try {
        const channel = await client.channels.fetch(LOG_CHANNEL_ID);
        if (!channel || !channel.isTextBased()) return;

        await channel.send({ embeds: [embed] });
    } catch (error) {
        console.error('[LOG ERROR] Failed to send log to Discord:', error.message);
    }
}
