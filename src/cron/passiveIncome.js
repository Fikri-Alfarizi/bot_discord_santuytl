import { query } from '../db/index.js';

export async function distributePassiveIncome(client) {
    const REWARD_PER_MINUTE = 60; // 1 RP per second x 60

    try {
        const onlineUsers = [];

        // Collect all online users from all guilds
        client.guilds.cache.forEach(guild => {
            guild.members.cache.forEach(member => {
                if (!member.user.bot) {
                    const status = member.presence?.status;
                    // 'online', 'idle', 'dnd' count as online
                    if (status === 'online' || status === 'idle' || status === 'dnd') {
                        onlineUsers.push({
                            id: member.user.id,
                            username: member.user.username
                        });
                    }
                }
            });
        });

        if (onlineUsers.length === 0) return;

        // UPSERT SQL for MySQL
        const sql = `
            INSERT INTO users (id, username, coins) 
            VALUES (?, ?, ${REWARD_PER_MINUTE})
            ON DUPLICATE KEY UPDATE 
            coins = coins + ${REWARD_PER_MINUTE},
            username = VALUES(username)
        `;

        // Run updates in parallel
        const updates = onlineUsers.map(user => {
            return query(sql, [user.id, user.username]);
        });

        await Promise.all(updates);
        // console.log(`[PASSIVE] Gave ${REWARD_PER_MINUTE} coins to ${onlineUsers.length} online users.`);

    } catch (error) {
        console.error('[PASSIVE ERROR]', error);
    }
}
