import { query, get } from '../db/index.js';

class SeasonService {
    async getCurrentSeason() {
        // MySQL LIMIT 1 return type depends on wrapper, our get() returns single row
        return await get('SELECT * FROM seasons WHERE is_active = 1 ORDER BY id DESC LIMIT 1', []);
    }

    async startNewSeason(name, daysDuration) {
        const current = await this.getCurrentSeason();
        if (current) {
            await this.endSeason(current.id);
        }

        const nextNumber = current ? current.season_number + 1 : 1;
        const startDate = Date.now();
        const endDate = startDate + (daysDuration * 24 * 60 * 60 * 1000);

        await query(`
            INSERT INTO seasons (season_number, name, start_date, end_date, is_active)
            VALUES (?, ?, ?, ?, 1)
        `, [nextNumber, name, startDate, endDate]);

        // Fetch newly created season
        return await this.getCurrentSeason();
    }

    async endSeason(seasonId) {
        await query('UPDATE seasons SET is_active = 0 WHERE id = ?', [seasonId]);
        // Here we could trigger rewards, snapshots, etc.
    }

    async getSeasonTimeLeft() {
        const season = await this.getCurrentSeason();
        if (!season) return null;

        const now = Date.now();
        if (now >= season.end_date) return 0;

        return season.end_date - now;
    }
}

export default new SeasonService();
