/**
 * Shop Items Configuration
 */

export const SHOP_ITEMS = [
    {
        id: 'role_vip',
        name: '👑 VIP Role (7 Hari)',
        description: 'Dapet role VIP selama 7 hari. Akses eksklusif!',
        price: 5000,
        type: 'role',
        duration: 7 * 24 * 60 * 60 * 1000, // 7 days in ms
        roleId: process.env.VIP_ROLE_ID || null
    },
    {
        id: 'color_custom',
        name: '🎨 Custom Color Role (3 Hari)',
        description: 'Pilih warna sendiri untuk role kamu!',
        price: 3000,
        type: 'custom_color',
        duration: 3 * 24 * 60 * 60 * 1000
    },
    {
        id: 'xp_boost',
        name: '⚡ Double XP (24 Jam)',
        description: 'XP kamu jadi 2x lipat selama 24 jam!',
        price: 2000,
        type: 'xp_boost',
        duration: 24 * 60 * 60 * 1000,
        multiplier: 2
    },
    {
        id: 'coin_boost',
        name: '💰 Double Coins (24 Jam)',
        description: 'Coins kamu jadi 2x lipat selama 24 jam!',
        price: 2000,
        type: 'coin_boost',
        duration: 24 * 60 * 60 * 1000,
        multiplier: 2
    },
    {
        id: 'rename_bot',
        name: '🤖 Rename Bot (1 Jam)',
        description: 'Ganti nama bot sesuai keinginan kamu (1 jam)',
        price: 10000,
        type: 'rename_bot',
        duration: 60 * 60 * 1000
    }
];

/**
 * Get item by ID
 */
export function getItemById(itemId) {
    return SHOP_ITEMS.find(item => item.id === itemId);
}

/**
 * Get all items by type
 */
export function getItemsByType(type) {
    return SHOP_ITEMS.filter(item => item.type === type);
}
