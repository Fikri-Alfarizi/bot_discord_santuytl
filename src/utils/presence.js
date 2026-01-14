import { ActivityType } from "discord.js";

export function startPresence(client) {
    const statuses = [
        { name: "/help | Panduan Lengkap", type: ActivityType.Playing },
        { name: "/spin | Game Premium", type: ActivityType.Playing },
        { name: "/job | Cari Cuan", type: ActivityType.Playing },
        { name: "/season | Season 1 Live!", type: ActivityType.Playing },
        { name: "SantuyTL Community", type: ActivityType.Watching }
    ];

    let index = 0;

    // Set initial status immediately
    if (client.user) {
        client.user.setActivity(statuses[0].name, { type: statuses[0].type });
    }

    setInterval(() => {
        if (!client.user) return; // Guard clause

        const status = statuses[index];

        client.user.setActivity(status.name, {
            type: status.type
        });

        index = (index + 1) % statuses.length;
    }, 15000); // Rotate every 15 seconds
}
