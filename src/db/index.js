import db, { query, get, all, transaction } from './sqlite.js';

// Table Initialization Script (Safe to run multiple times)
// We use 'CREATE TABLE IF NOT EXISTS'

export async function initDatabase() {
    console.log('[DB] Initializing SQLite Database...');

    try {
        // Users Table
        db.exec(`
            CREATE TABLE IF NOT EXISTS users (
                id TEXT PRIMARY KEY,
                username TEXT,
                xp INTEGER DEFAULT 0,
                level INTEGER DEFAULT 1,
                coins INTEGER DEFAULT 0,
                last_daily INTEGER DEFAULT 0,
                last_weekly INTEGER DEFAULT 0,
                is_afk INTEGER DEFAULT 0,
                afk_reason TEXT,
                afk_timestamp INTEGER DEFAULT 0,
                job TEXT DEFAULT 'Pengangguran',
                daily_spins INTEGER DEFAULT 0,
                last_spin_time INTEGER DEFAULT 0,
                seasonal_xp INTEGER DEFAULT 0
            )
        `);

        // Guild Settings
        db.exec(`
            CREATE TABLE IF NOT EXISTS guild_settings (
                guild_id TEXT PRIMARY KEY,
                welcome_channel_id TEXT,
                leave_channel_id TEXT,
                log_channel_id TEXT,
                game_source_channel_id TEXT,
                request_channel_id TEXT,
                news_channel_id TEXT,
                general_chat_channel_id TEXT,
                welcome_message TEXT,
                auto_role_id TEXT,
                welcome_enabled INTEGER DEFAULT 0,
                welcome_embed_enabled INTEGER DEFAULT 0,
                welcome_embed_title TEXT,
                welcome_embed_description TEXT,
                welcome_embed_color TEXT,
                welcome_embed_image TEXT,
                welcome_embed_thumbnail TEXT,
                welcome_dm_enabled INTEGER DEFAULT 0,
                welcome_dm_message TEXT,
                goodbye_enabled INTEGER DEFAULT 0,
                goodbye_channel_id TEXT,
                goodbye_message TEXT,
                goodbye_embed_enabled INTEGER DEFAULT 0,
                goodbye_embed_title TEXT,
                goodbye_embed_description TEXT,
                goodbye_embed_color TEXT,
                auto_role_enabled INTEGER DEFAULT 0
            )
        `);

        // News History
        db.exec(`
            CREATE TABLE IF NOT EXISTS news_history (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                news_guid TEXT UNIQUE,
                created_at INTEGER
            )
        `);

        // Big Server Features
        // 1. Seasons
        db.exec(`
            CREATE TABLE IF NOT EXISTS seasons (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                season_number INTEGER,
                name TEXT,
                start_date INTEGER,
                end_date INTEGER,
                is_active INTEGER DEFAULT 1
            )
        `);

        // 2. Reputation
        db.exec(`
            CREATE TABLE IF NOT EXISTS reputation (
                user_id TEXT PRIMARY KEY,
                rep_points INTEGER DEFAULT 0,
                last_given INTEGER DEFAULT 0
            )
        `);

        // 3. Trust Score
        db.exec(`
            CREATE TABLE IF NOT EXISTS trust_score (
                user_id TEXT PRIMARY KEY,
                score INTEGER DEFAULT 100,
                reason TEXT
            )
        `);

        // 5. Inventory
        db.exec(`
            CREATE TABLE IF NOT EXISTS inventory (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                user_id TEXT NOT NULL,
                item_id TEXT NOT NULL,
                quantity INTEGER DEFAULT 1,
                expires_at INTEGER,
                metadata TEXT,
                created_at INTEGER DEFAULT (strftime('%s', 'now'))
            )
        `);

        // 6. Reaction Roles
        db.exec(`
            CREATE TABLE IF NOT EXISTS reaction_roles (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                guild_id TEXT NOT NULL,
                message_id TEXT NOT NULL,
                channel_id TEXT NOT NULL,
                role_id TEXT NOT NULL,
                emoji TEXT NOT NULL,
                type TEXT DEFAULT 'normal',
                created_at INTEGER DEFAULT (strftime('%s', 'now'))
            )
        `);

        // 7. Moderator (Auto Mod & Warns)
        db.exec(`
            CREATE TABLE IF NOT EXISTS automod_rules (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                guild_id TEXT NOT NULL,
                trigger_type TEXT NOT NULL,
                trigger_content TEXT,
                action TEXT DEFAULT 'delete',
                enabled INTEGER DEFAULT 1
            )
        `);

        db.exec(`
            CREATE TABLE IF NOT EXISTS warns (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                guild_id TEXT NOT NULL,
                user_id TEXT NOT NULL,
                moderator_id TEXT NOT NULL,
                reason TEXT,
                timestamp INTEGER DEFAULT (strftime('%s', 'now'))
            )
        `);

        // Birthdays
        db.exec(`
            CREATE TABLE IF NOT EXISTS birthdays (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                guild_id TEXT NOT NULL,
                user_id TEXT NOT NULL,
                user_name TEXT,
                day INTEGER,
                month INTEGER,
                created_at INTEGER,
                updated_at INTEGER,
                UNIQUE(guild_id, user_id)
            )
        `);

        // Invites
        db.exec(`
            CREATE TABLE IF NOT EXISTS invites (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                guild_id TEXT NOT NULL,
                inviter_id TEXT NOT NULL,
                invited_id TEXT NOT NULL,
                code TEXT,
                timestamp INTEGER,
                is_valid INTEGER DEFAULT 1,
                is_fake INTEGER DEFAULT 0,
                created_at INTEGER,
                updated_at INTEGER,
                UNIQUE(inviter_id, invited_id)
            )
        `);

        // Level Rewards
        db.exec(`
            CREATE TABLE IF NOT EXISTS level_rewards (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                guild_id TEXT NOT NULL,
                level INTEGER NOT NULL,
                role_id TEXT NOT NULL,
                created_at INTEGER DEFAULT (strftime('%s', 'now'))
            )
        `);

        // Custom Commands
        db.exec(`
            CREATE TABLE IF NOT EXISTS custom_commands (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                guild_id TEXT NOT NULL,
                trigger TEXT NOT NULL,
                response TEXT,
                is_embed INTEGER DEFAULT 0,
                embed_data TEXT,
                created_at INTEGER DEFAULT (strftime('%s', 'now'))
            )
        `);

        // Automations
        db.exec(`
            CREATE TABLE IF NOT EXISTS automations (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                guild_id TEXT NOT NULL,
                event TEXT NOT NULL,
                trigger_value TEXT,
                action_type TEXT,
                action_value TEXT,
                is_active INTEGER DEFAULT 1,
                created_at INTEGER DEFAULT (strftime('%s', 'now'))
            )
        `);

        // Stats Channels (for server stats display)
        db.exec(`
            CREATE TABLE IF NOT EXISTS stats_channels (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                guild_id TEXT NOT NULL,
                channel_id TEXT NOT NULL,
                stat_type TEXT NOT NULL,
                format TEXT,
                created_at INTEGER DEFAULT (strftime('%s', 'now')),
                UNIQUE(guild_id, stat_type)
            )
        `);

        // Bot Outbox (for queued messages from Laravel)
        db.exec(`
            CREATE TABLE IF NOT EXISTS bot_outbox(
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                guild_id TEXT,
                channel_id TEXT NOT NULL,
                message_type TEXT DEFAULT 'text',
                content TEXT,
                embed_data TEXT,
                status TEXT DEFAULT 'pending',
                created_at INTEGER DEFAULT(strftime('%s', 'now')),
                sent_at INTEGER
            )
            `);

        // Reminders table
        db.exec(`
            CREATE TABLE IF NOT EXISTS reminders(
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                user_id TEXT NOT NULL,
                channel_id TEXT NOT NULL,
                message TEXT NOT NULL,
                remind_at TEXT NOT NULL,
                created_at INTEGER DEFAULT(strftime('%s', 'now'))
            )
            `);

        console.log('[DB] Tables initialized successfully.');
    } catch (error) {
        console.error('[DB ERROR] Init failed:', error);
    }
}

// Export the adapter methods for usage in services
export { query, get, all, transaction };
export default db;
