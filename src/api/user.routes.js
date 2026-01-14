import express from 'express';
import client from '../config/discord.js';
import userService from '../services/user.service.js';

const router = express.Router();

router.get('/users', async (req, res) => {
    try {
        const { guild_id } = req.query;
        let guild;
        if (guild_id) {
            guild = await client.guilds.fetch(guild_id).catch(() => null);
        } else {
            guild = client.guilds.cache.first();
        }

        if (!guild) return res.json({ users: [] });

        await guild.members.fetch();
        const users = guild.members.cache
            .filter(member => !member.user.bot)
            .map(member => ({
                id: member.user.id,
                username: member.user.username,
                discriminator: member.user.discriminator
            }));

        res.json({ users });
    } catch (err) {
        res.json({ users: [] });
    }
});

router.get('/users/:id', async (req, res) => {
    try {
        const userId = req.params.id;
        const guild = client.guilds.cache.first();
        if (!guild) return res.status(404).json({ error: 'Guild not found' });

        await guild.members.fetch();
        const member = guild.members.cache.get(userId);

        if (!member) {
            return res.status(404).json({ error: 'User not found' });
        }

        const roles = member.roles.cache
            .filter(role => role.id !== guild.id)
            .map(role => ({
                id: role.id,
                name: role.name,
                color: role.color.toString(16).padStart(6, '0'),
                position: role.position
            }))
            .sort((a, b) => b.position - a.position);

        const user = {
            id: member.user.id,
            username: member.user.username,
            discriminator: member.user.discriminator,
            avatar: member.user.avatar,
            joined_at: member.joinedAt,
            roles: roles,
            guild: guild.name,
            guild_id: guild.id
        };

        res.json({ user });
    } catch (err) {
        res.status(500).json({ error: err.message });
    }
});

router.get('/user-stats/:id', async (req, res) => {
    try {
        const userId = req.params.id;
        const guild = client.guilds.cache.first();
        if (!guild) return res.status(404).json({ error: 'Guild not found' });

        // Fetch discord member info
        await guild.members.fetch();
        const member = guild.members.cache.get(userId);
        if (!member) return res.status(404).json({ error: 'User not found' });

        // Get DB Stats
        const dbUser = userService.getUser(userId, member.user.username);

        const stats = {
            id: member.user.id,
            username: member.user.username,
            discriminator: member.user.discriminator,
            avatar: member.user.avatarURL({ dynamic: true, size: 128 }),
            joined_at: member.joinedAt,
            roles: member.roles.cache.filter(role => role.id !== guild.id).map(role => role.name),
            xp: dbUser.xp,
            level: dbUser.level,
            coins: dbUser.coins,
            last_daily: dbUser.last_daily,
            // Keep dummy or remove if not tracked? 
            // The original had voice_minutes, messages. We are not tracking them in DB yet.
            // I will set them to 0 or remove for now to avoid confusion.
            voice_minutes: 0,
            messages: 0,
            badges: [
                { name: 'Member', icon: '👤' }
            ],
            achievements: []
        };
        res.json({ stats });
    } catch (err) {
        res.status(500).json({ error: err.message });
    }
});

export default router;
