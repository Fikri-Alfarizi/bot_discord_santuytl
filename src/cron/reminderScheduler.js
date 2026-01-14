import { all, query } from '../db/index.js';
import { EmbedBuilder } from 'discord.js';

export function startReminderScheduler(client) {
    // Run every 1 minute
    setInterval(() => {
        checkReminders(client);
    }, 60 * 1000);
}

async function checkReminders(client) {
    try {
        // Fetch reminders due for running
        // Using MySQL NOW() might be timezone sensitive. Ideally use UNIX timestamp or ensure app/db timezones align.
        // Assuming database server time is used.
        const dueReminders = await all('SELECT * FROM reminders WHERE next_run_at <= NOW()');

        if (!dueReminders || dueReminders.length === 0) return;

        for (const reminder of dueReminders) {
            try {
                const guild = client.guilds.cache.get(reminder.guild_id);
                if (!guild) continue;

                const channel = guild.channels.cache.get(reminder.channel_id);
                if (channel) {
                    await channel.send({
                        content: `⏰ **Reminder:**\n${reminder.message}`
                    });
                    console.log(`[Reminder] Sent reminder ${reminder.id} to channel ${reminder.channel_id}`);
                } else {
                    console.warn(`[Reminder] Channel ${reminder.channel_id} not found for reminder ${reminder.id}`);
                }

                // Update next_run_at
                // In JS, calculate next time.
                // interval_minutes
                const nextRun = new Date(Date.now() + (reminder.interval_minutes * 60 * 1000));

                // Format to MySQL datetime: YYYY-MM-DD HH:MM:SS
                // Helper function
                const nextRunFormatted = nextRun.toISOString().slice(0, 19).replace('T', ' ');

                await query('UPDATE reminders SET last_sent_at = NOW(), next_run_at = ?, updated_at = NOW() WHERE id = ?',
                    [nextRunFormatted, reminder.id]);

            } catch (err) {
                console.error(`[Reminder] Error processing reminder ${reminder.id}:`, err);
            }
        }
    } catch (err) {
        console.error('[Reminder Scheduler] Error:', err);
    }
}
