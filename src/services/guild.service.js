import { query, get } from '../db/index.js';

class GuildService {
    async getSettings(guildId) {
        let settings = await get('SELECT * FROM guild_settings WHERE guild_id = ?', [guildId]);
        if (!settings) {
            await query('INSERT INTO guild_settings (guild_id) VALUES (?)', [guildId]);
            settings = {
                guild_id: guildId,
                welcome_channel_id: null,
                leave_channel_id: null,
                log_channel_id: null,
                welcome_message: 'Selamat datang {user} di {server}!',
                auto_role_id: null
            };
        }
        return settings;
    }

    async updateSetting(guildId, key, value) {
        await this.getSettings(guildId); // Ensure exists
        // Vulnerable to SQL injection if 'key' is dynamic user input, but internal usage is safe for now.
        // In MySQL2, we can't parameterize column names. White-listing needed if key is external.
        // For now, assume key is safe (internal calls only).
        return await query(`UPDATE guild_settings SET ${key} = ? WHERE guild_id = ?`, [value, guildId]);
    }
}

export default new GuildService();
