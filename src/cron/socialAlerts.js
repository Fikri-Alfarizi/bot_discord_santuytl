import Parser from 'rss-parser';
import { EmbedBuilder } from 'discord.js';
import { all, query } from '../db/index.js';

const parser = new Parser();

export function startSocialAlerts(client) {
    // Run every 10 minutes
    setInterval(() => {
        checkSocialAlerts(client);
    }, 10 * 60 * 1000);
}

async function checkSocialAlerts(client) {
    try {
        const alerts = await all('SELECT * FROM social_alerts');
        if (!alerts) return;

        for (const alert of alerts) {
            try {
                if (alert.platform === 'rss' || alert.platform === 'youtube') {
                    await checkRSS(client, alert);
                } else if (alert.platform === 'twitch') {
                    // await checkTwitch(client, alert);
                    // Twitch requires App Access Token. 
                    // For now skipping complex OAuth flow in this MVP step unless tokens are present.
                    console.log('[SocialAlerts] Twitch logic skipped (Requires OAuth implementation)');
                }
            } catch (err) {
                console.error(`[SocialAlerts] Error processing alert ${alert.id}:`, err);
            }
        }
    } catch (err) {
        console.error('[SocialAlerts] Error:', err);
    }
}

async function checkRSS(client, alert) {
    let url = alert.identifier;

    // Convert YouTube Channel ID to RSS URL
    if (alert.platform === 'youtube' && !url.includes('http')) {
        url = `https://www.youtube.com/feeds/videos.xml?channel_id=${alert.identifier}`;
    }

    try {
        const feed = await parser.parseURL(url);
        const latestItem = feed.items[0];

        if (!latestItem) return;

        // Check if new
        if (latestItem.id !== alert.last_alert_id && latestItem.link !== alert.last_alert_id) {
            const guild = client.guilds.cache.get(alert.guild_id);
            if (!guild) return;

            const channel = guild.channels.cache.get(alert.discord_channel_id);
            if (!channel) return;

            const message = alert.message.replace('{link}', latestItem.link);

            await channel.send(message);

            // Update last_alert_id
            const newId = latestItem.id || latestItem.link;
            await query('UPDATE social_alerts SET last_alert_id = ?, last_check_at = NOW(), updated_at = NOW() WHERE id = ?', [newId, alert.id]);

            console.log(`[SocialAlerts] Sent alert for ${alert.platform} ${alert.identifier} to ${channel.name}`);
        }
    } catch (err) {
        console.error(`[SocialAlerts] Failed to fetch RSS for ${alert.identifier}:`, err.message);
    }
}
