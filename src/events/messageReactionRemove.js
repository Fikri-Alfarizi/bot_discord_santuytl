import { Events } from 'discord.js';
import { get } from '../db/index.js';

export default {
    name: Events.MessageReactionRemove,
    async execute(reaction, user) {
        // Ignore bot reactions
        if (user.bot) return;

        // Fetch partials
        if (reaction.partial) {
            try {
                await reaction.fetch();
            } catch (error) {
                console.error('[Reaction Error] Something went wrong when fetching the message:', error);
                return;
            }
        }

        const { message, emoji } = reaction;
        const guild = message.guild;
        if (!guild) return;

        try {
            // Find in DB
            const emojiIdentifier = emoji.id || emoji.name;

            const rr = await get(
                'SELECT * FROM reaction_roles WHERE guild_id = ? AND message_id = ? AND emoji = ?',
                [guild.id, message.id, emojiIdentifier]
            );

            if (rr) {
                const member = await guild.members.fetch(user.id);
                if (member) {
                    await member.roles.remove(rr.role_id);
                    // console.log(`[Reaction Role] Removed role ${rr.role_id} from ${user.tag}`);
                }
            }
        } catch (error) {
            console.error('[Reaction Role Error]', error);
        }
    }
};
