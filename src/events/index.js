import fs from 'fs';
import path from 'path';
import { fileURLToPath } from 'url';

const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);

export async function loadEvents(client) {
    const eventFiles = fs.readdirSync(__dirname).filter(file => file.endsWith('.js') && file !== 'index.js');

    for (const file of eventFiles) {
        const filePath = path.join(__dirname, file);
        const event = await import(`file://${filePath}`);

        if (event.default && event.default.once) {
            client.once(event.default.name, (...args) => event.default.execute(...args));
        } else if (event.default) {
            client.on(event.default.name, (...args) => event.default.execute(...args));
        }
    }
}
