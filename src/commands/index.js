import 'dotenv/config';
import fs from 'fs';
import path from 'path';
import { REST, Routes, Collection } from 'discord.js';
import { fileURLToPath } from 'url';

const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);

export async function loadCommands(client) {
    client.commands = new Collection();
    const commandFiles = fs.readdirSync(__dirname).filter(file => file.endsWith('.js') && file !== 'index.js');

    for (const file of commandFiles) {
        const filePath = path.join(__dirname, file);
        const command = await import(`file://${filePath}`); // Windows compatible
        if ('data' in command && 'execute' in command) {
            client.commands.set(command.data.name, command);
        } else {
            console.log(`[WARNING] The command at ${filePath} is missing a required "data" or "execute" property.`);
        }
    }
}

// Deploy script to be run separately
if (process.argv[1] === __filename) {
    (async () => {
        const commands = [];
        const commandFiles = fs.readdirSync(__dirname).filter(file => file.endsWith('.js') && file !== 'index.js');

        for (const file of commandFiles) {
            const filePath = path.join(__dirname, file);
            const command = await import(`file://${filePath}`);
            if ('data' in command && 'execute' in command) {
                commands.push(command.data.toJSON());
            }
        }

        const rest = new REST({ version: '10' }).setToken(process.env.DISCORD_BOT_TOKEN);

        try {
            console.log(`Started refreshing ${commands.length} application (/) commands.`);

            const clientId = process.env.DISCORD_CLIENT_ID;
            const guildId = process.env.DISCORD_GUILD_ID;

            if (!clientId) {
                console.error('Error: DISCORD_CLIENT_ID is missing in .env');
                process.exit(1);
            }

            // Deploy globally by default for multi-server accessibility
            console.log('Deploying commands globally for all servers...');
            await rest.put(
                Routes.applicationCommands(clientId),
                { body: commands },
            );

            console.log('Successfully reloaded application (/) commands.');
        } catch (error) {
            console.error(error);
        }
    })();
}
