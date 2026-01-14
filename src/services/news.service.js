import Parser from 'rss-parser';
import { query, get, all } from '../db/index.js';
import guildService from './guild.service.js';

const parser = new Parser();

// News sources to monitor
const NEWS_SOURCES = [
    {
        name: 'Steam News',
        url: 'https://store.steampowered.com/feeds/news.xml',
        icon: 'https://store.steampowered.com/favicon.ico',
        color: 0x1B2838
    },
    {
        name: 'CrackWatch',
        url: 'https://www.reddit.com/r/CrackWatch/.rss',
        icon: 'https://www.redditstatic.com/desktop2x/img/favicon/android-icon-192x192.png',
        color: 0xFF4500
    }
];

/**
 * Check and post news to configured channels
 * @param {Client} client - Discord client
 */
export async function checkAndPostNews(client) {
    console.log('[NEWS] Checking for new articles...');

    for (const source of NEWS_SOURCES) {
        try {
            const feed = await parser.parseURL(source.url);
            const items = feed.items.slice(0, 5); // Only check latest 5

            for (const item of items) {
                // Check if already posted
                const exists = await get('SELECT id FROM news_history WHERE news_guid = ?', [item.guid || item.link]);

                if (!exists) {
                    // Save to DB
                    await query('INSERT INTO news_history (news_guid, created_at) VALUES (?, ?)', [item.guid || item.link, Date.now()]);

                    // Get all guilds with news configured
                    const guilds = await all('SELECT guild_id, news_channel_id FROM guild_settings WHERE news_channel_id IS NOT NULL');

                    for (const guild of guilds) {
                        try {
                            const channel = await client.channels.fetch(guild.news_channel_id);
                            if (channel) {
                                const embed = {
                                    author: { name: source.name, icon_url: source.icon },
                                    title: item.title,
                                    url: item.link,
                                    description: item.contentSnippet
                                        ? item.contentSnippet.substring(0, 300) + '...'
                                        : 'Klik link untuk baca selengkapnya.',
                                    color: source.color,
                                    footer: { text: `Posted: ${new Date(item.pubDate).toLocaleString()}` },
                                    timestamp: new Date()
                                };

                                await channel.send({ embeds: [embed] });
                            }
                        } catch (err) {
                            console.error(`[NEWS ERROR] Guild ${guild.guild_id}: ${err.message}`);
                        }
                    }
                }
            }
        } catch (error) {
            console.error(`[NEWS ERROR] Fetching ${source.name}: ${error.message}`);
        }
    }
}

export default { checkAndPostNews };
