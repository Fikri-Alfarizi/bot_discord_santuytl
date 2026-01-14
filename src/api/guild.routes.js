import express from 'express';
import client from '../config/discord.js';

const router = express.Router();

async function getGuild(req) {
    const { guild_id } = req.query;
    if (guild_id) {
        return await client.guilds.fetch(guild_id);
    }
    return process.env.DISCORD_GUILD_ID ? await client.guilds.fetch(process.env.DISCORD_GUILD_ID) : client.guilds.cache.first();
}

router.get('/guilds', async (req, res) => {
    try {
        const guilds = client.guilds.cache.map(guild => ({
            id: guild.id,
            name: guild.name,
            icon: guild.icon ? `https://cdn.discordapp.com/icons/${guild.id}/${guild.icon}.png` : null,
            memberCount: guild.memberCount,
            ownerId: guild.ownerId
        }));
        res.json({ guilds });
    } catch (err) {
        res.status(500).json({ error: err.message });
    }
});

router.get('/server-stats', async (req, res) => {
    try {
        const guild = await getGuild(req);
        if (!guild) return res.status(404).json({ error: 'No guild found' });

        await guild.members.fetch();
        const onlineCount = guild.members.cache.filter(member =>
            ['online', 'idle', 'dnd'].includes(member.presence?.status)
        ).size;

        await guild.channels.fetch();
        await guild.roles.fetch();

        res.json({
            guild_id: guild.id,
            guild_name: guild.name,
            guild_icon: guild.icon ? `https://cdn.discordapp.com/icons/${guild.id}/${guild.icon}.png` : null,
            member_count: guild.memberCount,
            online_count: onlineCount,
            boost_level: guild.premiumTier,
            boost_count: guild.premiumSubscriptionCount || 0,
            channel_count: guild.channels.cache.size,
            text_channel_count: guild.channels.cache.filter(c => c.type === 0).size,
            voice_channel_count: guild.channels.cache.filter(c => c.type === 2).size,
            role_count: guild.roles.cache.size - 1,
            created_at: guild.createdAt.toISOString(),
            owner_id: guild.ownerId
        });
    } catch (err) {
        res.status(500).json({ error: err.message });
    }
});

router.get('/events', async (req, res) => {
    try {
        const guild = await getGuild(req);
        if (!guild) return res.status(404).json({ error: 'No guild found' });

        await guild.scheduledEvents.fetch();
        const events = guild.scheduledEvents.cache.map(event => ({
            id: event.id,
            name: event.name,
            description: event.description,
            scheduled_start_time: event.scheduledStartTimestamp,
            scheduled_end_time: event.scheduledEndTimestamp,
            status: event.status,
            image: event.image ? `https://cdn.discordapp.com/guilds/${guild.id}/scheduled-events/${event.id}/${event.image}.png` : null,
            creator_id: event.creatorId,
            entity_type: event.entityType,
            url: event.url,
            channel_id: event.channelId,
        }));
        res.json({ events });
    } catch (err) {
        res.status(500).json({ error: err.message });
    }
});

router.get('/channels', async (req, res) => {
    try {
        const channels = [];
        const { guild_id } = req.query;
        if (guild_id) {
            const guild = await client.guilds.fetch(guild_id);
            await guild.channels.fetch();
            guild.channels.cache.filter(c => c.type === 0).forEach(c => {
                channels.push({ id: c.id, name: c.name, guild: guild.name, guild_id: guild.id });
            });
        } else {
            for (const [id, guild] of client.guilds.cache) {
                await guild.channels.fetch();
                guild.channels.cache.filter(c => c.type === 0).forEach(c => {
                    channels.push({ id: c.id, name: c.name, guild: guild.name, guild_id: guild.id });
                });
            }
        }
        res.json({ channels });
    } catch (err) {
        res.status(500).json({ error: err.message });
    }
});

router.get('/roles', async (req, res) => {
    try {
        const roles = [];
        const { guild_id } = req.query;
        if (guild_id) {
            const guild = await client.guilds.fetch(guild_id);
            await guild.roles.fetch();
            guild.roles.cache.filter(r => r.name !== '@everyone').forEach(r => {
                roles.push({ id: r.id, name: r.name, guild: guild.name, guild_id: guild.id });
            });
        } else {
            for (const [id, guild] of client.guilds.cache) {
                await guild.roles.fetch();
                guild.roles.cache.filter(r => r.name !== '@everyone').forEach(r => {
                    roles.push({ id: r.id, name: r.name, guild: guild.name, guild_id: guild.id });
                });
            }
        }
        res.json({ roles });
    } catch (err) {
        res.status(500).json({ error: err.message });
    }
});

router.get('/staff', async (req, res) => {
    try {
        const guild = await getGuild(req);
        if (!guild) return res.status(404).json({ error: 'No guild found' });

        await guild.members.fetch({ withPresences: true });
        await guild.roles.fetch();

        const staffRoles = ['Owner', 'Moderator', 'Admin'];

        const staffMembers = guild.members.cache
            .filter(member =>
                !member.user.bot &&
                member.roles.cache.some(role => staffRoles.includes(role.name))
            )
            .map(member => ({
                id: member.user.id,
                username: member.user.username,
                discriminator: member.user.discriminator,
                avatar: member.user.avatarURL({ dynamic: true, size: 128 }),
                display_name: member.displayName,
                joined_at: member.joinedAt,
                roles: member.roles.cache
                    .filter(role => role.id !== guild.id)
                    .map(role => ({
                        id: role.id,
                        name: role.name,
                        color: role.color.toString(16).padStart(6, '0'),
                        position: role.position
                    }))
                    .sort((a, b) => b.position - a.position)
            }));

        res.json({ staff: staffMembers });
    } catch (err) {
        res.status(500).json({ error: err.message });
    }
});

// Example specific channel fetch
router.get('/game-requests', async (req, res) => {
    try {
        // Should customize this ID or use env
        const channelId = '1385912786395336875';
        const channel = await client.channels.fetch(channelId);

        let messages = [];
        let lastId;
        while (true) {
            const options = { limit: 100 };
            if (lastId) options.before = lastId;
            const fetched = await channel.messages.fetch(options);
            if (fetched.size === 0) break;
            messages = messages.concat(Array.from(fetched.values()));
            lastId = fetched.last().id;
            if (fetched.size < 100) break;
        }

        const data = messages.map(msg => ({
            id: msg.id,
            author: msg.author.username,
            content: msg.content,
            timestamp: msg.createdTimestamp
        }));
        res.json({ requests: data });
    } catch (err) {
        res.status(500).json({ requests: [] });
    }
});

export default router;
