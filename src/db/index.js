import pool, { query, get, all, transaction } from './mysql.js';

// Table Initialization Script (Safe to run multiple times)
// We use 'CREATE TABLE IF NOT EXISTS'

export async function initDatabase() {
    console.log('[DB] Initializing MySQL Database...');

    const connection = await pool.getConnection();
    try {
        // Users Table
        await connection.query(`
            CREATE TABLE IF NOT EXISTS users (
                id VARCHAR(255) PRIMARY KEY,
                username VARCHAR(255),
                xp INT DEFAULT 0,
                level INT DEFAULT 1,
                coins INT DEFAULT 0,
                last_daily BIGINT DEFAULT 0,
                last_weekly BIGINT DEFAULT 0,
                is_afk TINYINT DEFAULT 0,
                afk_reason TEXT,
                afk_timestamp BIGINT DEFAULT 0,
                job VARCHAR(255) DEFAULT 'Pengangguran',
                daily_spins INT DEFAULT 0,
                last_spin_time BIGINT DEFAULT 0,
                seasonal_xp INT DEFAULT 0
            )
        `);

        // Guild Settings
        await connection.query(`
            CREATE TABLE IF NOT EXISTS guild_settings (
                guild_id VARCHAR(255) PRIMARY KEY,
                welcome_channel_id VARCHAR(255),
                leave_channel_id VARCHAR(255),
                log_channel_id VARCHAR(255),
                game_source_channel_id VARCHAR(255),
                request_channel_id VARCHAR(255),
                news_channel_id VARCHAR(255),
                general_chat_channel_id VARCHAR(255),
                welcome_message TEXT,
                auto_role_id VARCHAR(255),
                welcome_enabled TINYINT DEFAULT 0,
                welcome_embed_enabled TINYINT DEFAULT 0,
                welcome_embed_title VARCHAR(255),
                welcome_embed_description TEXT,
                welcome_embed_color VARCHAR(10),
                welcome_embed_image TEXT,
                welcome_embed_thumbnail TEXT,
                welcome_dm_enabled TINYINT DEFAULT 0,
                welcome_dm_message TEXT,
                goodbye_enabled TINYINT DEFAULT 0,
                goodbye_channel_id VARCHAR(255),
                goodbye_message TEXT,
                goodbye_embed_enabled TINYINT DEFAULT 0,
                goodbye_embed_title VARCHAR(255),
                goodbye_embed_description TEXT,
                goodbye_embed_color VARCHAR(10),
                auto_role_enabled TINYINT DEFAULT 0
            )
        `);

        // News History
        await connection.query(`
            CREATE TABLE IF NOT EXISTS news_history (
                id INT AUTO_INCREMENT PRIMARY KEY,
                news_guid VARCHAR(255) UNIQUE,
                created_at BIGINT
            )
        `);

        // Big Server Features
        // 1. Seasons
        await connection.query(`
            CREATE TABLE IF NOT EXISTS seasons (
                id INT AUTO_INCREMENT PRIMARY KEY,
                season_number INT,
                name VARCHAR(255),
                start_date BIGINT,
                end_date BIGINT,
                is_active TINYINT DEFAULT 1
            )
        `);

        // 2. Reputation
        await connection.query(`
            CREATE TABLE IF NOT EXISTS reputation (
                user_id VARCHAR(255) PRIMARY KEY,
                rep_points INT DEFAULT 0,
                last_given BIGINT DEFAULT 0
            )
        `);

        // 3. Trust Score
        await connection.query(`
            CREATE TABLE IF NOT EXISTS trust_score (
                user_id VARCHAR(255) PRIMARY KEY,
                score INT DEFAULT 100,
                reason TEXT
            )
        `);

        // 4. Invites 
        // Managed by Laravel Migration (2025_01_01_000010_create_invites_table.php)
        /*
        await connection.query(`
            CREATE TABLE IF NOT EXISTS invites (
                id INT AUTO_INCREMENT PRIMARY KEY,
                inviter_id VARCHAR(255),
                invited_id VARCHAR(255),
                timestamp BIGINT,
                is_valid TINYINT DEFAULT 1,
                UNIQUE KEY unique_invite (inviter_id, invited_id)
            )
        `);
        */

        // 5. Inventory
        await connection.query(`
            CREATE TABLE IF NOT EXISTS inventory (
                id INT AUTO_INCREMENT PRIMARY KEY,
                user_id VARCHAR(255) NOT NULL,
                item_id VARCHAR(255) NOT NULL,
                quantity INT DEFAULT 1,
                expires_at BIGINT,
                metadata TEXT,
                created_at BIGINT DEFAULT (UNIX_TIMESTAMP())
            )
        `);

        // 6. Reaction Roles
        await connection.query(`
            CREATE TABLE IF NOT EXISTS reaction_roles (
                id INT AUTO_INCREMENT PRIMARY KEY,
                guild_id VARCHAR(255) NOT NULL,
                message_id VARCHAR(255) NOT NULL,
                channel_id VARCHAR(255) NOT NULL,
                role_id VARCHAR(255) NOT NULL,
                emoji VARCHAR(255) NOT NULL,
                type VARCHAR(50) DEFAULT 'normal',
                created_at BIGINT DEFAULT (UNIX_TIMESTAMP())
            )
        `);

        // 7. Moderator (Auto Mod & Warns)
        await connection.query(`
            CREATE TABLE IF NOT EXISTS automod_rules (
                id INT AUTO_INCREMENT PRIMARY KEY,
                guild_id VARCHAR(255) NOT NULL,
                trigger_type VARCHAR(50) NOT NULL, -- bad_word, link, spam
                trigger_content TEXT, -- JSON array or comma separated
                action VARCHAR(50) DEFAULT 'delete', -- delete, timeout, kick, ban
                enabled TINYINT DEFAULT 1
            )
        `);

        await connection.query(`
            CREATE TABLE IF NOT EXISTS warns (
                id INT AUTO_INCREMENT PRIMARY KEY,
                guild_id VARCHAR(255) NOT NULL,
                user_id VARCHAR(255) NOT NULL,
                moderator_id VARCHAR(255) NOT NULL,
                reason TEXT,
                timestamp BIGINT DEFAULT (UNIX_TIMESTAMP())
            )
        `);

        console.log('[DB] Tables initialized successfully.');
    } catch (error) {
        console.error('[DB ERROR] Init failed:', error);
    } finally {
        connection.release();
    }
}

// Export the adapter methods for usage in services
export { query, get, all, transaction };
export default pool;
