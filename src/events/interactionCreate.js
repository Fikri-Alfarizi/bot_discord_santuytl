import { ActionRowBuilder, ButtonBuilder, ButtonStyle, EmbedBuilder, PermissionFlagsBits } from 'discord.js';
import { get, query } from '../db/index.js';

export default {
    name: 'interactionCreate',
    async execute(interaction) {
        // Handle Chat Commands
        if (interaction.isChatInputCommand()) {
            const command = interaction.client.commands.get(interaction.commandName);
            if (!command) return;

            try {
                await command.execute(interaction);
            } catch (error) {
                console.error(error);
                const reply = { content: '❌ Error executing command!', ephemeral: true };
                if (interaction.replied || interaction.deferred) await interaction.followUp(reply);
                else await interaction.reply(reply);
            }
        }

        // Handle Buttons
        else if (interaction.isButton()) {

            // --- 🎉 GIVEAWAY JOIN ---
            if (interaction.isButton() && interaction.customId.startsWith('giveaway_join_')) {
                const giveawayId = interaction.customId.replace('giveaway_join_', '');

                try {
                    // Check if already joined
                    const existing = await get('SELECT * FROM giveaway_participants WHERE giveaway_id = ? AND user_id = ?', [giveawayId, interaction.user.id]);

                    if (existing) {
                        return interaction.reply({ content: 'You already joined this giveaway!', ephemeral: true });
                    }

                    // Check if active
                    const giveaway = await get('SELECT * FROM giveaways WHERE id = ?', [giveawayId]);
                    if (!giveaway || giveaway.status !== 'active') {
                        return interaction.reply({ content: 'This giveaway has ended.', ephemeral: true });
                    }

                    await query('INSERT INTO giveaway_participants (giveaway_id, user_id, user_name, created_at, updated_at) VALUES (?, ?, ?, NOW(), NOW())',
                        [giveawayId, interaction.user.id, interaction.user.username]);

                    await interaction.reply({ content: '🎉 You have successfully joined the giveaway!', ephemeral: true });

                } catch (e) {
                    console.error('[Giveaway Error]', e);
                    await interaction.reply({ content: 'Error joining giveaway.', ephemeral: true });
                }
            }

            // --- 🎟️ TICKETING SYSTEM ---
            if (interaction.customId === 'ticket_create') {
                await interaction.deferReply({ ephemeral: true });

                try {
                    // Check if user already has open ticket
                    const existing = await get('SELECT * FROM tickets WHERE guild_id = ? AND user_id = ? AND status = "open"', [interaction.guild.id, interaction.user.id]);
                    if (existing) {
                        return interaction.editReply({ content: `❌ Kamu sudah punya tiket terbuka: <#${existing.channel_id}>` });
                    }

                    // Get Config
                    const config = await get('SELECT * FROM ticket_configs WHERE guild_id = ?', [interaction.guild.id]);
                    if (!config || !config.category_id) {
                        return interaction.editReply({ content: '❌ Sistem tiket belum dikonfigurasi sepenuhnya oleh Admin.' });
                    }

                    // Create Channel
                    const channelName = `ticket-${interaction.user.username}`.toLowerCase().replace(/[^a-z0-9]/g, '-');
                    const supportRole = interaction.guild.roles.cache.get(config.support_role_id);

                    const channel = await interaction.guild.channels.create({
                        name: channelName,
                        type: 0, // GuildText
                        parent: config.category_id,
                        permissionOverwrites: [
                            {
                                id: interaction.guild.id,
                                deny: [PermissionFlagsBits.ViewChannel],
                            },
                            {
                                id: interaction.user.id,
                                allow: [PermissionFlagsBits.ViewChannel, PermissionFlagsBits.SendMessages, PermissionFlagsBits.AttachFiles],
                            },
                            {
                                id: config.support_role_id, // If role invalid, this might fail, handled in catch
                                allow: [PermissionFlagsBits.ViewChannel, PermissionFlagsBits.SendMessages],
                            },
                            // Bot itself needs access
                            {
                                id: interaction.client.user.id,
                                allow: [PermissionFlagsBits.ViewChannel, PermissionFlagsBits.SendMessages],
                            }
                        ],
                    });

                    // Save to DB
                    const uuid = Math.random().toString(36).substring(2, 15);
                    await query('INSERT INTO tickets (uuid, guild_id, user_id, channel_id, status, created_at, updated_at) VALUES (?, ?, ?, ?, "open", NOW(), NOW())',
                        [uuid, interaction.guild.id, interaction.user.id, channel.id]);

                    // Send Welcome Message
                    const embed = new EmbedBuilder()
                        .setTitle(`Ticket #${uuid}`)
                        .setDescription(config.ticket_message || `Halo ${interaction.user}, staf kami akan segera membantu anda.`)
                        .setColor('#6c63ff')
                        .setTimestamp();

                    const row = new ActionRowBuilder()
                        .addComponents(
                            new ButtonBuilder()
                                .setCustomId(`ticket_close_${uuid}`)
                                .setLabel('Close Ticket')
                                .setStyle(ButtonStyle.Danger)
                                .setEmoji('🔒')
                        );

                    await channel.send({ content: `${interaction.user} | <@&${config.support_role_id}>`, embeds: [embed], components: [row] });

                    await interaction.editReply({ content: `✅ Tiket berhasil dibuat: ${channel}` });

                } catch (error) {
                    console.error('Error creating ticket:', error);
                    await interaction.editReply({ content: '❌ Terjadi kesalahan saat membuat tiket.' });
                }
            }

            else if (interaction.customId.startsWith('ticket_close_')) {
                // Confirm Close? For simplicity, just close.
                // Or better: ask for confirmation, but let's do direct close for MVP.
                // Steps:
                // 1. Generate Transcript (Buffer content)
                // 2. Send to Log Channel
                // 3. Delete Channel
                // 4. Update DB

                await interaction.reply({ content: '🔒 Menutup tiket dalam 5 detik...' });

                const uuid = interaction.customId.split('_')[2];
                const channel = interaction.channel;
                const ticket = await get('SELECT * FROM tickets WHERE uuid = ?', [uuid]);

                if (!ticket) return; // Weird state

                // Fetch Config for Logs
                const config = await get('SELECT * FROM ticket_configs WHERE guild_id = ?', [interaction.guild.id]);

                // Generate Transcript (Simple)
                if (config && config.log_channel_id) {
                    const logChannel = interaction.guild.channels.cache.get(config.log_channel_id);
                    if (logChannel) {
                        try {
                            const messages = await channel.messages.fetch({ limit: 100 });
                            let transcript = `TRANSCRIPT FOR TICKET #${uuid}\n`;
                            transcript += `User: ${ticket.user_id}\n`;
                            transcript += `Closed By: ${interaction.user.tag}\n`;
                            transcript += `Date: ${new Date().toLocaleString()}\n\n`;

                            messages.reverse().forEach(m => {
                                transcript += `[${m.createdAt.toLocaleString()}] ${m.author.tag}: ${m.content}\n`;
                                if (m.attachments.size > 0) transcript += `[Attachment: ${m.attachments.first().url}]\n`;
                            });

                            const buffer = Buffer.from(transcript, 'utf-8');

                            await logChannel.send({
                                content: `📝 **Ticket Closed**\nTicket #${uuid} closed by ${interaction.user}.`,
                                files: [{ attachment: buffer, name: `transcript-${uuid}.txt` }]
                            });
                        } catch (e) {
                            console.error('Failed to save transcript:', e);
                        }
                    }
                }

                setTimeout(async () => {
                    await query('UPDATE tickets SET status = "closed", updated_at = NOW() WHERE uuid = ?', [uuid]);
                    await channel.delete().catch(() => { });
                }, 5000);
            }

            // VERIFIKASI SISTEM (Existing)
            else if (interaction.customId.startsWith('verify_btn_')) {
                const roleId = interaction.customId.split('_')[2];
                const role = interaction.guild.roles.cache.get(roleId);

                if (!role) {
                    return interaction.reply({ content: '❌ Role tidak ditemukan/sudah dihapus!', ephemeral: true });
                }

                // Cek apakah user sudah punya role
                if (interaction.member.roles.cache.has(roleId)) {
                    return interaction.reply({ content: '✅ Kamu sudah terverifikasi!', ephemeral: true });
                }

                try {
                    await interaction.member.roles.add(role);
                    await interaction.reply({
                        content: `🎉 **Selamat Datang!** Kamu berhasil verifikasi dan mendapatkan role **${role.name}**.`,
                        ephemeral: true
                    });
                } catch (error) {
                    console.error(error);
                    await interaction.reply({ content: '❌ Bot gagal memberi role. Pastikan role Bot lebih tinggi dari role target!', ephemeral: true });
                }
            }

            // POLL VOTING
            else if (interaction.customId.startsWith('poll_vote_')) {
                await interaction.deferReply({ ephemeral: true });

                const optionIndex = parseInt(interaction.customId.split('_')[2]);
                const messageId = interaction.message.id;
                const userId = interaction.user.id;
                const guildId = interaction.guild.id;
                const channelId = interaction.channel.id;

                try {
                    // Check if already voted
                    const existingVote = await get('SELECT * FROM poll_votes WHERE message_id = ? AND user_id = ?', [messageId, userId]);

                    if (existingVote) {
                        if (existingVote.option_index === optionIndex) {
                            return interaction.editReply({ content: '⚠️ Kamu sudah memilih opsi ini!' });
                        } else {
                            // Update vote
                            await query('UPDATE poll_votes SET option_index = ? WHERE id = ?', [optionIndex, existingVote.id]);
                            await interaction.editReply({ content: '✅ Pilihanmu telah diubah!' });
                        }
                    } else {
                        // Insert vote
                        await query('INSERT INTO poll_votes (guild_id, channel_id, message_id, user_id, option_index, created_at, updated_at) VALUES (?, ?, ?, ?, ?, NOW(), NOW())',
                            [guildId, channelId, messageId, userId, optionIndex]);
                        await interaction.editReply({ content: '✅ Suaramu telah tercatat!' });
                    }

                    // Update Embed Stats
                    const allVotes = await query('SELECT option_index, COUNT(*) as count FROM poll_votes WHERE message_id = ? GROUP BY option_index', [messageId]);

                    // Reconstruct counts map
                    const counts = {};
                    let totalVotes = 0;
                    allVotes.forEach(v => {
                        counts[v.option_index] = v.count;
                        totalVotes += v.count;
                    });

                    // Get original Embed
                    const originalEmbed = interaction.message.embeds[0];
                    const descriptionLines = originalEmbed.description.split('\n\n');

                    // Rebuild description with percentages
                    const newDescription = descriptionLines.map((line, index) => {
                        // Original line format: "1️⃣ Option Text" or "1️⃣ Option Text (X% - Y votes)"
                        // We need to clean it first to just "1️⃣ Option Text"
                        const cleanLine = line.replace(/\s\([0-9.]+% - [0-9]+ votes\)$/, '');

                        const voteCount = counts[index] || 0;
                        const percentage = totalVotes > 0 ? ((voteCount / totalVotes) * 100).toFixed(1) : 0;

                        return `${cleanLine} (${percentage}% - ${voteCount} votes)`;
                    }).join('\n\n');

                    const newEmbed = EmbedBuilder.from(originalEmbed)
                        .setDescription(newDescription)
                        .setFooter({ text: `Total Votes: ${totalVotes}` });

                    await interaction.message.edit({ embeds: [newEmbed] });

                } catch (error) {
                    console.error('Poll Vote Error:', error);
                    await interaction.editReply({ content: '❌ Terjadi kesalahan saat voting.' });
                }
            }
        }
    },
};
