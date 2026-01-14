import { query, get } from '../db/index.js';

class TrustService {
    async getTrustScore(userId) {
        const data = await get('SELECT score, reason FROM trust_score WHERE user_id = ?', [userId]);
        return data ? data.score : 100; // Default 100
    }

    async deductTrust(userId, amount, reason) {
        const currentScore = await this.getTrustScore(userId);
        const newScore = Math.max(0, currentScore - amount); // Floor at 0

        // Use UPSERT for atomic update
        await query(`
            INSERT INTO trust_score (user_id, score, reason) 
            VALUES (?, ?, ?)
            ON DUPLICATE KEY UPDATE score = ?, reason = ?
        `, [userId, newScore, reason, newScore, reason]);

        return newScore;
    }

    // Observer Logic: Call this on message/interaction events
    async observeUserBehavior(userId, activityType) {
        // Simple heuristic for now
        // activityType: 'spam', 'exploit_attempt', 'toxic_language'

        let penalty = 0;
        let reason = '';

        if (activityType === 'spam') {
            penalty = 5;
            reason = 'Detected Spamming';
        } else if (activityType === 'exploit_attempt') {
            penalty = 20;
            reason = 'Attempted System Exploit';
        }

        if (penalty > 0) {
            await this.deductTrust(userId, penalty, reason);
        }
    }
}

export default new TrustService();
