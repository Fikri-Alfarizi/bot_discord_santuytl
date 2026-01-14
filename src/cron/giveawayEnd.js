import { EmbedBuilder } from 'discord.js';
import { query, all } from '../db/index.js';

export const startGiveawayScheduler = (client) => {
    setInterval(async () => {
        try {
            const now = new Date();
            const endedGiveaways = await all('SELECT * FROM giveaways WHERE status = ? AND end_at <= ?', ['active', now]);

            if (!endedGiveaways || endedGiveaways.length === 0) return;

            for (const giveaway of endedGiveaways) {
                const participants = await all('SELECT * FROM giveaway_participants WHERE giveaway_id = ?', [giveaway.id]);
                const channel = client.channels.cache.get(giveaway.channel_id);

                let winners = [];
                let winnerText = 'No valid entries.';

                if (participants.length > 0) {
                    // shuffle
                    const shuffled = participants.sort(() => 0.5 - Math.random());
                    winners = shuffled.slice(0, giveaway.winner_count);
                    winnerText = winners.map(w => `<@${w.user_id}>`).join(', ');
                }

                if (channel) {
                    // Update original message
                    try {
                        const msg = await channel.messages.fetch(giveaway.message_id);
                        if (msg) {
                            const embed = new EmbedBuilder(msg.embeds[0].data);
                            embed.setTitle('🎉 GIVEAWAY ENDED 🎉');
                            embed.setDescription(`**Prize**: ${giveaway.prize}\n\n🏆 **Winners**: ${winnerText}\n❌ **Ended**: <t:${Math.floor(Date.now() / 1000)}:R>`);
                            embed.setColor('#2c2f33'); // Grey

                            // Remove buttons
                            await msg.edit({ embeds: [embed], components: [] });
                        }
                    } catch (e) { console.error('Failed to edit giveaway msg:', e.message); }

                    // Send announcement
                    if (winners.length > 0) {
                        await channel.send(`🎉 Congratulations ${winnerText}! You won **${giveaway.prize}**! 🎁`);
                    } else {
                        await channel.send(`🎉 Giveaway for **${giveaway.prize}** ended with no participants.`);
                    }
                }

                // Update DB
                await query('UPDATE giveaways SET status = ?, ended_at = NOW() WHERE id = ?', ['ended', giveaway.id]);
            }

        } catch (error) {
            console.error('[Giveaway Scheduler Error]', error);
        }
    }, 60 * 1000); // Check every minute
};
