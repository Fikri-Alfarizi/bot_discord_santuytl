<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PluginsController extends Controller
{
    /**
     * Show all plugins page
     */
    public function index()
    {
        $plugins = $this->getPlugins();
        return view('plugins.index', compact('plugins'));
    }

    /**
     * Get all plugins grouped by category
     */
    private function getPlugins()
    {
        return [
            'Essentials' => [
                ['id' => 'welcome', 'name' => 'Welcome & Goodbye', 'fa_icon' => 'fa-solid fa-hand', 'desc' => 'Kirim pesan dan berikan role ke member baru, serta pesan saat member keluar.', 'status' => 'active', 'premium' => true, 'route' => 'welcome.index'],
                ['id' => 'welcome_channel', 'name' => 'Welcome Channel', 'fa_icon' => 'fa-solid fa-door-open', 'desc' => 'Channel khusus untuk menyambut member baru dengan informasi penting.', 'status' => 'active', 'premium' => true, 'route' => 'welcome-channel.index'],
                ['id' => 'reaction_roles', 'name' => 'Reaction Roles', 'fa_icon' => 'fa-solid fa-face-smile', 'desc' => 'Biarkan member mendapat role dengan react ke pesan.', 'status' => 'active', 'premium' => true, 'route' => 'reaction-roles.index'],
                ['id' => 'moderator', 'name' => 'Moderator', 'fa_icon' => 'fa-solid fa-shield-halved', 'desc' => 'Jaga keamanan server dengan auto-moderasi dan tools moderasi yang powerful.', 'status' => 'active', 'premium' => true, 'route' => 'moderator.index'],
                ['id' => 'levels', 'name' => 'Levels', 'fa_icon' => 'fa-solid fa-arrow-up-right-dots', 'desc' => 'Berikan XP dan Level ke member saat mereka chat dan ranking berdasarkan aktivitas.', 'status' => 'active', 'premium' => true, 'route' => 'levels.index'],
                ['id' => 'achievements', 'name' => 'Achievements', 'fa_icon' => 'fa-solid fa-medal', 'desc' => 'Biarkan member berburu achievements untuk mendapat rewards.', 'status' => 'active', 'premium' => false],
            ],
            'Server Management' => [
                ['id' => 'automations', 'name' => 'Automations', 'fa_icon' => 'fa-solid fa-robot', 'desc' => 'Otomatisasi aksi bot sebagai respons terhadap event server.', 'status' => 'active', 'premium' => true, 'route' => 'automations.index'],
                ['id' => 'custom_commands', 'name' => 'Custom Commands', 'fa_icon' => 'fa-solid fa-terminal', 'desc' => 'Buat command teks dan command yang memberikan role tertentu.', 'status' => 'active', 'premium' => true, 'route' => 'custom-commands.index'],
                ['id' => 'invite_tracker', 'name' => 'Invite Tracker', 'fa_icon' => 'fa-solid fa-link', 'desc' => 'Track berapa banyak orang yang diundang member ke komunitas.', 'status' => 'active', 'premium' => false, 'new' => true, 'route' => 'invite-tracker.index'],
                ['id' => 'ticketing', 'name' => 'Ticketing', 'fa_icon' => 'fa-solid fa-ticket', 'desc' => 'Izinkan member mengirim tiket untuk support, laporan, dan request.', 'status' => 'active', 'premium' => true, 'route' => 'ticketing.index'],
            ],
            'Utilities' => [
                ['id' => 'emojis', 'name' => 'Emojis', 'fa_icon' => 'fa-regular fa-face-grin', 'desc' => 'Tingkatkan server dengan menambahkan custom emojis.', 'status' => 'active', 'premium' => false, 'new' => true, 'route' => 'emojis.index'],
                ['id' => 'polls', 'name' => 'Polls', 'fa_icon' => 'fa-solid fa-square-poll-vertical', 'desc' => 'Izinkan member membuat polls dan voting.', 'status' => 'active', 'premium' => true, 'route' => 'polls.index'],
                ['id' => 'embed', 'name' => 'Embed Messages', 'fa_icon' => 'fa-solid fa-code', 'desc' => 'Buat pesan embed cantik untuk rules dan pengumuman.', 'status' => 'active', 'premium' => true, 'route' => 'embed.index'],
                ['id' => 'search', 'name' => 'Search Anything', 'fa_icon' => 'fa-solid fa-magnifying-glass', 'desc' => 'Aktifkan command untuk mencari video YouTube, streamer Twitch, anime & lainnya.', 'status' => 'active', 'premium' => false],
                ['id' => 'record', 'name' => 'Record', 'fa_icon' => 'fa-solid fa-microphone', 'desc' => 'Rekam percakapan voice channel member dalam 1 klik.', 'status' => 'soon', 'premium' => false],
                ['id' => 'reminders', 'name' => 'Reminders', 'fa_icon' => 'fa-regular fa-clock', 'desc' => 'Kirim pesan custom secara berulang setiap beberapa menit/jam.', 'status' => 'active', 'premium' => false, 'route' => 'reminders.index'],
                ['id' => 'stats_channels', 'name' => 'Statistics Channels', 'fa_icon' => 'fa-solid fa-chart-simple', 'desc' => 'Tampilkan stats server dan follower sosmed di sidebar channel.', 'status' => 'active', 'premium' => true, 'route' => 'stats-channels.index'],
                ['id' => 'temp_channels', 'name' => 'Temporary Channels', 'fa_icon' => 'fa-solid fa-volume-high', 'desc' => 'Izinkan member membuat voice channel sementara dalam 1 klik.', 'status' => 'active', 'premium' => true, 'route' => 'temp-channels.index'],
            ],
            'Social Alerts' => [
                ['id' => 'twitch', 'name' => 'Twitch Alerts', 'fa_icon' => 'fa-brands fa-twitch', 'desc' => 'Kirim notifikasi otomatis saat kamu live di Twitch.', 'status' => 'active', 'premium' => true, 'route' => 'twitch.index'],
                ['id' => 'tiktok', 'name' => 'TikTok Alerts', 'fa_icon' => 'fa-brands fa-tiktok', 'desc' => 'Kirim notifikasi otomatis saat ada video TikTok baru.', 'status' => 'active', 'premium' => true, 'new' => true, 'route' => 'tiktok.index'],
                ['id' => 'x_alerts', 'name' => 'X Alerts', 'fa_icon' => 'fa-brands fa-x-twitter', 'desc' => 'Kirim notifikasi otomatis saat ada tweet baru.', 'status' => 'active', 'premium' => true, 'route' => 'x.index'],
                ['id' => 'youtube', 'name' => 'YouTube Alerts', 'fa_icon' => 'fa-brands fa-youtube', 'desc' => 'Kirim notifikasi otomatis saat ada video YouTube baru.', 'status' => 'active', 'premium' => true, 'route' => 'youtube.index'],
                ['id' => 'reddit', 'name' => 'Reddit Alerts', 'fa_icon' => 'fa-brands fa-reddit-alien', 'desc' => 'Kirim notifikasi saat ada post baru di Reddit.', 'status' => 'active', 'premium' => true, 'route' => 'reddit.index'],
                ['id' => 'instagram', 'name' => 'Instagram Alerts', 'fa_icon' => 'fa-brands fa-instagram', 'desc' => 'Subscribe ke user Instagram dan dapat notifikasi post baru.', 'status' => 'active', 'premium' => true, 'route' => 'instagram.index'],
                ['id' => 'rss', 'name' => 'RSS Feeds', 'fa_icon' => 'fa-solid fa-rss', 'desc' => 'Kirim pesan otomatis saat ada item baru di RSS feed.', 'status' => 'active', 'premium' => false, 'route' => 'rss.index'],
                ['id' => 'kick', 'name' => 'Kick Alerts', 'fa_icon' => 'fa-solid fa-bolt', 'desc' => 'Kirim notifikasi saat ada yang mulai live stream di Kick.', 'status' => 'active', 'premium' => true, 'route' => 'kick.index'],
            ],
            'Games & Fun' => [
                ['id' => 'giveaways', 'name' => 'Giveaways', 'fa_icon' => 'fa-solid fa-gift', 'desc' => 'Buat giveaway dan lotere di server dalam 1 klik.', 'status' => 'active', 'premium' => true, 'route' => 'giveaways.index'],
                ['id' => 'birthdays', 'name' => 'Birthdays', 'fa_icon' => 'fa-solid fa-cake-candles', 'desc' => 'Track ulang tahun member dan otomatis ucapkan selamat.', 'status' => 'active', 'premium' => true, 'route' => 'birthdays.index'],
                ['id' => 'music_quiz', 'name' => 'Music Quiz', 'fa_icon' => 'fa-solid fa-music', 'desc' => 'Main kuis musik di voice channel. Tebak nama lagu dan menang poin!', 'status' => 'soon', 'premium' => true],
                ['id' => 'economy', 'name' => 'Economy', 'fa_icon' => 'fa-solid fa-coins', 'desc' => 'Member dapat koin dengan !daily, !work, !weekly dan game lainnya.', 'status' => 'active', 'premium' => true],
            ],
            'AI Features' => [
                ['id' => 'ai_chat', 'name' => 'AI Chat', 'fa_icon' => 'fa-solid fa-brain', 'desc' => 'Chat dengan AI yang bisa membantu member dengan berbagai pertanyaan.', 'status' => 'active', 'premium' => false],
                ['id' => 'ai_characters', 'name' => 'AI Characters', 'fa_icon' => 'fa-solid fa-masks-theater', 'desc' => 'Buat karakter AI custom untuk roleplay dan interaksi.', 'status' => 'soon', 'premium' => true],
                ['id' => 'ai_moderator', 'name' => 'AI Moderator', 'fa_icon' => 'fa-solid fa-user-shield', 'desc' => 'AI yang otomatis moderasi chat dari konten toxic.', 'status' => 'soon', 'premium' => true],
            ],
            'Monetization' => [
                ['id' => 'monetize', 'name' => 'Monetize', 'fa_icon' => 'fa-solid fa-money-bill-wave', 'desc' => 'Hasilkan uang dari server Discord dengan subscription dan role premium.', 'status' => 'soon', 'premium' => true],
                ['id' => 'donations', 'name' => 'Donations', 'fa_icon' => 'fa-solid fa-heart', 'desc' => 'Terima donasi dari member dengan integrasi payment.', 'status' => 'soon', 'premium' => true],
            ],
        ];
    }
}
