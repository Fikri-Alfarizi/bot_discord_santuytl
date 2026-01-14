import { query, get } from '../db/index.js';

class UserService {
    async getUser(userId, username = 'Unknown', avatar = null) {
        let user = await get('SELECT * FROM users WHERE id = ?', [userId]);
        if (!user) {
            await query('INSERT INTO users (id, username, xp, level, coins, last_daily, avatar) VALUES (?, ?, 0, 1, 0, 0, ?)', [userId, username, avatar]);
            user = { id: userId, username, xp: 0, level: 1, coins: 0, last_daily: 0, avatar };
        }
        return user;
    }

    async addXp(userId, username, amount, avatar = null) {
        const user = await this.getUser(userId, username, avatar);
        let newXp = user.xp + amount;
        let newLevel = user.level;

        // Simple level up formula: Level * 100 XP
        const xpNeeded = user.level * 100;
        let leveledUp = false;

        if (newXp >= xpNeeded) {
            newXp -= xpNeeded;
            newLevel++;
            leveledUp = true;
        }

        await query('UPDATE users SET xp = ?, seasonal_xp = seasonal_xp + ?, level = ?, username = ?, avatar = ? WHERE id = ?',
            [newXp, amount, newLevel, username, avatar, userId]
        );

        return { ...user, xp: newXp, level: newLevel, leveledUp };
    }

    async addCoins(userId, username, amount) {
        const user = await this.getUser(userId, username);
        const newCoins = user.coins + amount;

        await query('UPDATE users SET coins = ? WHERE id = ?', [newCoins, userId]);

        return { ...user, coins: newCoins };
    }

    async getLeaderboard(limit = 10) {
        // mysql2 executes returns [rows, fields], our 'all' wrapper returns rows
        const { all } = await import('../db/index.js');
        return await all('SELECT * FROM users ORDER BY level DESC, xp DESC LIMIT ?', [limit]);
    }

    // --- REWARD SYSTEM ---

    async checkDaily(userId) {
        // We only need last_daily, optimize query if possible, but getUser is safe
        const user = await this.getUser(userId);
        const now = Date.now();
        const cooldown = 24 * 60 * 60 * 1000; // 24 hours
        const lastDaily = user.last_daily || 0;

        if (now - lastDaily < cooldown) {
            return { available: false, remaining: cooldown - (now - lastDaily) };
        }
        return { available: true };
    }

    async claimDaily(userId, username, amount) {
        const check = await this.checkDaily(userId);
        if (!check.available) return false;

        const now = Date.now();
        await this.addCoins(userId, username, amount);
        await query('UPDATE users SET last_daily = ? WHERE id = ?', [now, userId]);
        return true;
    }

    async checkWeekly(userId) {
        const user = await this.getUser(userId);
        const now = Date.now();
        const cooldown = 7 * 24 * 60 * 60 * 1000; // 7 days
        const lastWeekly = user.last_weekly || 0;

        if (now - lastWeekly < cooldown) {
            return { available: false, remaining: cooldown - (now - lastWeekly) };
        }
        return { available: true };
    }

    async claimWeekly(userId, username, amount) {
        const check = await this.checkWeekly(userId);
        if (!check.available) return false;

        const now = Date.now();
        await this.addCoins(userId, username, amount);
        await query('UPDATE users SET last_weekly = ? WHERE id = ?', [now, userId]);
        return true;
    }

    // --- AFK SYSTEM ---

    async setAfk(userId, username, reason) {
        await this.getUser(userId, username); // Ensure user exists
        const now = Date.now();
        await query('UPDATE users SET is_afk = 1, afk_reason = ?, afk_timestamp = ? WHERE id = ?', [reason, now, userId]);
    }

    async removeAfk(userId) {
        await query('UPDATE users SET is_afk = 0, afk_reason = NULL, afk_timestamp = 0 WHERE id = ?', [userId]);
    }

    async getAfkStatus(userId) {
        const user = await get('SELECT is_afk, afk_reason, afk_timestamp FROM users WHERE id = ?', [userId]);
        return user || { is_afk: 0 };
    }
}

export default new UserService();
