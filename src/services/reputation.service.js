import { query, get } from '../db/index.js';

class ReputationService {
    async getReputation(userId) {
        const data = await get('SELECT rep_points, last_given FROM reputation WHERE user_id = ?', [userId]);
        return data || { rep_points: 0, last_given: 0 };
    }

    async giveReputation(giverId, receiverId) {
        const giverData = await this.getReputation(giverId);
        const now = Date.now();
        const COOLDOWN = 24 * 60 * 60 * 1000; // 24 Hours

        if (now - giverData.last_given < COOLDOWN) {
            const timeLeft = COOLDOWN - (now - giverData.last_given);
            const hours = Math.ceil(timeLeft / (1000 * 60 * 60));
            return { success: false, message: `Sabar bro! Lo baru bisa kasih Rep lagi dalam ${hours} jam.` };
        }

        if (giverId === receiverId) {
            return { success: false, message: "Gak bisa kasih Rep ke diri sendiri lah, kocak!" };
        }

        // Update Giver's cooldown logic - using SQLite UPSERT
        const giverExists = await get('SELECT user_id FROM reputation WHERE user_id = ?', [giverId]);
        if (giverExists) {
            await query('UPDATE reputation SET last_given = ? WHERE user_id = ?', [now, giverId]);
        } else {
            await query('INSERT INTO reputation (user_id, rep_points, last_given) VALUES (?, 0, ?)', [giverId, now]);
        }

        // Add Point to Receiver logic - using SQLite UPSERT
        const receiverExists = await get('SELECT user_id, rep_points FROM reputation WHERE user_id = ?', [receiverId]);
        if (receiverExists) {
            await query('UPDATE reputation SET rep_points = rep_points + 1 WHERE user_id = ?', [receiverId]);
        } else {
            await query('INSERT INTO reputation (user_id, rep_points, last_given) VALUES (?, 1, 0)', [receiverId]);
        }

        return { success: true, message: "Respect +1! Reputasi berhasil dikirim." };
    }
}

export default new ReputationService();
