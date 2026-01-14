import { query, all } from '../db/index.js';

export const startBirthdayAnnouncer = (client) => {
    // Check every day at 00:00 (or every hour to be safe, but only announce once per year)
    // For simplicity, run every hour and check if announced this year
    setInterval(async () => {
        try {
            const now = new Date();
            const currentDay = now.getDate();
            const currentMonth = now.getMonth() + 1; // 0-indexed in JS
            const currentYear = now.getFullYear();

            // Find birthdays today that haven't been announced this year
            const birthdays = await all('SELECT * FROM birthdays WHERE day = ? AND month = ? AND last_announced_year < ?',
                [currentDay, currentMonth, currentYear]);

            if (!birthdays || birthdays.length === 0) return;

            // Group by guild to reduce DB calls for settings
            const birthdaysByGuild = {};
            for (const b of birthdays) {
                if (!birthdaysByGuild[b.guild_id]) birthdaysByGuild[b.guild_id] = [];
                birthdaysByGuild[b.guild_id].push(b);
            }

            for (const guildId in birthdaysByGuild) {
                const guildApp = client.guilds.cache.get(guildId);
                if (!guildApp) continue;

                const settings = await all('SELECT birthday_channel_id, birthday_message FROM guild_settings WHERE guild_id = ?', [guildId]);
                if (!settings || settings.length === 0) continue;

                const { birthday_channel_id, birthday_message } = settings[0];
                if (!birthday_channel_id) continue;

                const channel = guildApp.channels.cache.get(birthday_channel_id);
                if (channel) {
                    for (const b of birthdaysByGuild[guildId]) {
                        const message = (birthday_message || 'Happy Birthday {user}! 🎉')
                            .replace('{user}', `<@${b.user_id}>`);

                        await channel.send(message);

                        // Mark announced
                        await query('UPDATE birthdays SET last_announced_year = ? WHERE id = ?', [currentYear, b.id]);
                        console.log(`[Birthday] Announced for ${b.user_name} in ${guildApp.name}`);
                    }
                }
            }

        } catch (error) {
            console.error('[Birthday Announcer Error]', error);
        }
    }, 60 * 60 * 1000); // Check every hour
};
