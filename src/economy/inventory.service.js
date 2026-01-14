import { query, get, all } from '../db/index.js';

class InventoryService {
    /**
     * Add item to user inventory
     */
    async addItem(userId, itemId, expiresAt = null, metadata = null) {
        return await query(`
            INSERT INTO inventory (user_id, item_id, expires_at, metadata)
            VALUES (?, ?, ?, ?)
        `, [userId, itemId, expiresAt, metadata ? JSON.stringify(metadata) : null]);
    }

    /**
     * Get user inventory
     */
    async getUserInventory(userId) {
        const now = Math.floor(Date.now() / 1000);
        const items = await all(`
            SELECT * FROM inventory 
            WHERE user_id = ? 
            AND (expires_at IS NULL OR expires_at > ?)
            ORDER BY created_at DESC
        `, [userId, now]);

        return items.map(item => ({
            ...item,
            metadata: item.metadata ? JSON.parse(item.metadata) : null
        }));
    }

    /**
     * Check if user has item
     */
    async hasItem(userId, itemId) {
        const now = Math.floor(Date.now() / 1000);
        const result = await get(`
            SELECT COUNT(*) as count FROM inventory 
            WHERE user_id = ? AND item_id = ?
            AND (expires_at IS NULL OR expires_at > ?)
        `, [userId, itemId, now]);

        return result.count > 0;
    }

    /**
     * Use/consume item
     */
    async useItem(userId, itemId) {
        // Find one valid item to delete (limit 1)
        // PostgreSQL/MySQL delete limit syntax varies slightly but LIMIT 1 works in MySQL
        return await query(`
            DELETE FROM inventory 
            WHERE id = (
                SELECT id FROM (
                    SELECT id FROM inventory 
                    WHERE user_id = ? AND item_id = ?
                    LIMIT 1
                ) as subquery
            )
        `, [userId, itemId]);
    }

    /**
     * Remove expired items
     */
    async cleanExpiredItems() {
        const now = Math.floor(Date.now() / 1000);
        return await query(`
            DELETE FROM inventory 
            WHERE expires_at IS NOT NULL AND expires_at <= ?
        `, [now]);
    }
}

export default new InventoryService();
