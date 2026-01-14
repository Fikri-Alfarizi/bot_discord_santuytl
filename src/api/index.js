import express from 'express';
import guildRoutes from './guild.routes.js';
import userRoutes from './user.routes.js';
import moderationRoutes from './moderation.routes.js';
import notifyRoutes from './notify.routes.js';

const router = express.Router();

router.use('/', guildRoutes); // endpoints like /guilds, /server-stats are often root in original
router.use('/', userRoutes); // /users, /user-stats
router.use('/', moderationRoutes); // /kick, /ban
router.use('/', notifyRoutes); // /gacha/notify etc

// Also map some explicit paths if they don't fit the prefix completely, 
// but in the original they were all root-ish or specific.
// I will try to group them better in the route files with router.get('/') inside the file if mounted at '/' or proper subpaths.
// Actually, to avoid breaking legacy Laravel calls, I should match the original paths exacty.
// Original: /voice/channels -> voice.routes (mounted at /voice, handle /channels)
// Original: /guilds -> guild.routes (mounted at /, handle /guilds)

export default router;
