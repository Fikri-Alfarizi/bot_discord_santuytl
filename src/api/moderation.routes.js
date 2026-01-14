import express from 'express';
import client from '../config/discord.js';

const router = express.Router();

router.post('/kick', async (req, res) => {
    const { guild_id, user_id, reason } = req.body;
    if (!guild_id || !user_id) return res.status(400).json({ error: 'guild_id dan user_id wajib diisi' });
    try {
        const guild = await client.guilds.fetch(guild_id);
        const member = await guild.members.fetch(user_id);
        await member.kick(reason || 'Kicked by admin');
        res.json({ success: true });
    } catch (err) {
        res.status(500).json({ error: err.message });
    }
});

router.post('/ban', async (req, res) => {
    const { guild_id, user_id, reason } = req.body;
    if (!guild_id || !user_id) return res.status(400).json({ error: 'guild_id dan user_id wajib diisi' });
    try {
        const guild = await client.guilds.fetch(guild_id);
        await guild.members.ban(user_id, { reason: reason || 'Banned by admin' });
        res.json({ success: true });
    } catch (err) {
        res.status(500).json({ error: err.message });
    }
});

router.post('/assign-role', async (req, res) => {
    const { guild_id, user_id, role_id } = req.body;
    if (!guild_id || !user_id || !role_id) return res.status(400).json({ error: 'guild_id, user_id, role_id wajib diisi' });
    try {
        const guild = await client.guilds.fetch(guild_id);
        const member = await guild.members.fetch(user_id);
        await member.roles.add(role_id);
        res.json({ success: true });
    } catch (err) {
        res.status(500).json({ error: err.message });
    }
});

router.post('/remove-role', async (req, res) => {
    const { guild_id, user_id, role_id } = req.body;
    if (!guild_id || !user_id || !role_id) return res.status(400).json({ error: 'guild_id, user_id, role_id wajib diisi' });
    try {
        const guild = await client.guilds.fetch(guild_id);
        const member = await guild.members.fetch(user_id);
        await member.roles.remove(role_id);
        res.json({ success: true });
    } catch (err) {
        res.status(500).json({ error: err.message });
    }
});

export default router;
