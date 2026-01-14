import mysql from 'mysql2/promise';
import 'dotenv/config';

// Create a connection pool to handle multiple concurrent queries (Scalability)
const pool = mysql.createPool({
    host: process.env.DB_HOST || 'localhost',
    user: process.env.DB_USER || 'root',
    password: process.env.DB_PASSWORD || '',
    database: process.env.DB_NAME || 'santuytl_bot',
    waitForConnections: true,
    connectionLimit: 10,
    queueLimit: 0,
    // Keep alive settings
    enableKeepAlive: true,
    keepAliveInitialDelay: 0
});

// Helper function to simulate SQLite's db.prepare().run() / .get() syntax
// This helps minimize refactoring pain, but we MUST switch to await.
export async function query(sql, params) {
    const [results] = await pool.execute(sql, params);
    return results;
}

// Wrapper for single row fetch
export async function get(sql, params) {
    const [results] = await pool.execute(sql, params);
    return results[0] || null;
}

// Wrapper for all rows fetch
export async function all(sql, params) {
    const [results] = await pool.execute(sql, params);
    return results;
}

// Transaction helper
export async function transaction(callback) {
    const connection = await pool.getConnection();
    try {
        await connection.beginTransaction();
        await callback(connection);
        await connection.commit();
    } catch (err) {
        await connection.rollback();
        throw err;
    } finally {
        connection.release();
    }
}

export default pool;
