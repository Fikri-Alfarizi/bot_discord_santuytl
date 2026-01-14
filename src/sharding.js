import { ShardingManager } from 'discord.js';
import path from 'path';
import { fileURLToPath } from 'url';
import 'dotenv/config';

const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);

const manager = new ShardingManager(path.join(__dirname, 'index.js'), {
    token: process.env.DISCORD_BOT_TOKEN,
    totalShards: 'auto', // Automatically spawn needed shards (1 per ~2500 servers)
    respawn: true
});

manager.on('shardCreate', shard => {
    console.log(`[SHARD] Launched Shard ${shard.id}`);
});

manager.spawn().catch(error => {
    console.error('[SHARD ERROR] Failed to spawn shards:', error);
});
