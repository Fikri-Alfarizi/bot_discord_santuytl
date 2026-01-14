import Database from 'better-sqlite3';
import path from 'path';
import { fileURLToPath } from 'url';
import fs from 'fs';

const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);

// Create data directory if it doesn't exist
const dataDir = path.join(__dirname, '../../data');
if (!fs.existsSync(dataDir)) {
    fs.mkdirSync(dataDir, { recursive: true });
}

const dbPath = path.join(dataDir, 'bot.db');
const db = new Database(dbPath);

// Enable WAL mode for better performance
db.pragma('journal_mode = WAL');

// Helper function to simulate mysql2's async interface
export async function query(sql, params = []) {
    try {
        const stmt = db.prepare(sql);
        if (sql.trim().toUpperCase().startsWith('SELECT')) {
            return stmt.all(...params);
        } else {
            return stmt.run(...params);
        }
    } catch (error) {
        console.error('[SQLite Error]', error.message);
        throw error;
    }
}

// Wrapper for single row fetch
export async function get(sql, params = []) {
    try {
        const stmt = db.prepare(sql);
        return stmt.get(...params) || null;
    } catch (error) {
        console.error('[SQLite Error]', error.message);
        throw error;
    }
}

// Wrapper for all rows fetch
export async function all(sql, params = []) {
    try {
        const stmt = db.prepare(sql);
        return stmt.all(...params);
    } catch (error) {
        console.error('[SQLite Error]', error.message);
        throw error;
    }
}

// Transaction helper
export async function transaction(callback) {
    const tx = db.transaction(() => {
        callback(db);
    });
    tx();
}

// Close database on process exit
process.on('exit', () => db.close());
process.on('SIGINT', () => {
    db.close();
    process.exit(0);
});

export default db;
