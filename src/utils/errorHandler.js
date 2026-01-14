/**
 * Global Error Handler untuk Discord Bot
 */

export class BotError extends Error {
    constructor(message, type = 'GENERAL') {
        super(message);
        this.type = type;
        this.name = 'BotError';
    }
}

/**
 * Handle command errors dengan embed yang keren
 */
export async function handleCommandError(interaction, error) {
    console.error(`[ERROR] Command: ${interaction.commandName}`, error);

    const errorEmbed = {
        title: '⚠️ **TERJADI KESALAHAN!**',
        description: `Waduh, ada yang error nih saat eksekusi command.\n\n**Error:** \`${error.message}\``,
        color: 0xFF0000,
        footer: { text: 'Kalau error terus, lapor ke admin ya!' },
        timestamp: new Date()
    };

    try {
        if (interaction.deferred || interaction.replied) {
            await interaction.followUp({ embeds: [errorEmbed], ephemeral: true });
        } else {
            await interaction.reply({ embeds: [errorEmbed], ephemeral: true });
        }
    } catch (followUpError) {
        console.error('[ERROR] Failed to send error message:', followUpError);
    }
}

/**
 * Handle general errors
 */
export function handleError(error, context = 'Unknown') {
    console.error(`[ERROR] Context: ${context}`, {
        message: error.message,
        stack: error.stack,
        timestamp: new Date().toISOString()
    });
}

/**
 * Log error ke Discord channel (opsional)
 */
export async function logErrorToChannel(client, error, context) {
    const LOG_CHANNEL_ID = process.env.ERROR_LOG_CHANNEL_ID;
    if (!LOG_CHANNEL_ID) return;

    try {
        const channel = await client.channels.fetch(LOG_CHANNEL_ID);
        if (!channel || !channel.isTextBased()) return;

        const logEmbed = {
            title: '🚨 **BOT ERROR LOG**',
            fields: [
                { name: 'Context', value: `\`${context}\``, inline: false },
                { name: 'Error', value: `\`\`\`${error.message}\`\`\``, inline: false },
                { name: 'Stack', value: `\`\`\`${error.stack?.substring(0, 1000) || 'N/A'}\`\`\``, inline: false }
            ],
            color: 0xFF0000,
            timestamp: new Date()
        };

        await channel.send({ embeds: [logEmbed] });
    } catch (logError) {
        console.error('[ERROR] Failed to log error to channel:', logError);
    }
}
