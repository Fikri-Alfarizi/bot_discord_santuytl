import 'dotenv/config';

// Optional: Add validation logic here if needed
if (!process.env.DISCORD_BOT_TOKEN) {
  console.warn('⚠️  Warning: DISCORD_BOT_TOKEN is missing in .env');
}

export default {
    token: process.env.DISCORD_BOT_TOKEN,
    clientId: process.env.DISCORD_CLIENT_ID,
    guildId: process.env.DISCORD_GUILD_ID,
    webhookUrl: process.env.WEBHOOK_URL,
    botSecret: process.env.DISCORD_BOT_SECRET,
    port: process.env.PORT || 3001
};
