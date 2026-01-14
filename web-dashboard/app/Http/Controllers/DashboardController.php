<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class DashboardController extends Controller
{
    /**
     * Get guild member IDs from Discord API
     */
    /**
     * Get guild members and sync to database
     */
    private function getGuildMemberIds($guildId)
    {
        $botToken = env('DISCORD_BOT_TOKEN');
        if (!$botToken || !$guildId) return [];

        try {
            $response = Http::withHeaders([
                'Authorization' => "Bot {$botToken}",
            ])->get("https://discord.com/api/guilds/{$guildId}/members", [
                'limit' => 1000
            ]);

            if ($response->successful()) {
                $members = $response->json();
                $ids = [];

                foreach ($members as $member) {
                    $user = $member['user'];
                    $ids[] = $user['id'];

                    // Sync user data if exists in db
                    // Use update instead of updateOrInsert to avoid creating non-existent users
                    // We only want to update avatars for users who are already in our levels system
                    DB::table('users')
                        ->where('id', $user['id'])
                        ->update([
                            'username' => $user['username'],
                            'avatar' => $user['avatar'] ?? null,
                        ]);
                }

                return $ids;
            }
        } catch (\Exception $e) {
            // Silent fail
        }

        return [];
    }

    /**
     * Dashboard Home - Show server stats
     */
    public function index()
    {
        $guilds = session('discord_guilds', []);
        $selectedGuildId = session('selected_guild_id', $guilds[0]['id'] ?? null);

        // Get member IDs from selected guild
        $memberIds = $this->getGuildMemberIds($selectedGuildId);

        // Get stats from bot database - FILTERED by guild members
        $stats = [
            'total_users' => 0,
            'total_coins' => 0,
            'total_xp' => 0,
            'avg_level' => 0,
            'top_users' => collect(),
            'recent_users' => collect(),
        ];

        try {
            if (DB::getSchemaBuilder()->hasTable('users')) {
                if (!empty($memberIds)) {
                    // Filter by guild member IDs
                    $stats = [
                        'total_users' => DB::table('users')->whereIn('id', $memberIds)->count(),
                        'total_coins' => DB::table('users')->whereIn('id', $memberIds)->sum('coins') ?? 0,
                        'total_xp' => DB::table('users')->whereIn('id', $memberIds)->sum('xp') ?? 0,
                        'avg_level' => round(DB::table('users')->whereIn('id', $memberIds)->avg('level') ?? 0, 1),
                        'top_users' => DB::table('users')
                            ->whereIn('id', $memberIds)
                            ->orderByDesc('level')
                            ->orderByDesc('xp')
                            ->limit(5)
                            ->get(['id', 'username', 'level', 'xp', 'coins']),
                        'recent_users' => DB::table('users')
                            ->whereIn('id', $memberIds)
                            ->orderByDesc('last_daily')
                            ->limit(5)
                            ->get(['username', 'level', 'last_daily']),
                    ];
                } else {
                    // No member data, show empty
                    $stats['top_users'] = collect();
                    $stats['recent_users'] = collect();
                }
            }
        } catch (\Exception $e) {
            // Tables don't exist yet
        }

        return view('dashboard.index', [
            'guilds' => $guilds,
            'selectedGuildId' => $selectedGuildId,
            'stats' => $stats,
        ]);
    }

    /**
     * Leaderboard Page - FILTERED by guild
     */
    public function leaderboard()
    {
        $selectedGuildId = session('selected_guild_id');
        $memberIds = $this->getGuildMemberIds($selectedGuildId);

        if (!empty($memberIds)) {
            $leaderboard = DB::table('users')
                ->whereIn('id', $memberIds)
                ->orderByDesc('level')
                ->orderByDesc('xp')
                ->limit(50)
                ->get(['id', 'username', 'level', 'xp', 'coins', 'seasonal_xp', 'avatar']);
        } else {
            $leaderboard = collect();
        }

        return view('dashboard.leaderboard', [
            'leaderboard' => $leaderboard,
        ]);
    }

    /**
     * Settings Page
     */
    public function settings()
    {
        $guilds = session('discord_guilds', []);
        $selectedGuildId = session('selected_guild_id');

        // Get current settings from database
        $currentSettings = DB::table('guild_settings')
            ->where('guild_id', $selectedGuildId)
            ->first();

        // Fetch channels and roles from Discord API using BOT token
        $channels = [];
        $roles = [];
        $botToken = env('DISCORD_BOT_TOKEN');
        
        if ($selectedGuildId && $botToken) {
            try {
                // Fetch channels using Bot token
                $channelsResponse = Http::withHeaders([
                    'Authorization' => "Bot {$botToken}",
                ])->timeout(5)->get("https://discord.com/api/guilds/{$selectedGuildId}/channels");
                
                if ($channelsResponse->successful()) {
                    $allChannels = $channelsResponse->json();
                    foreach ($allChannels as $channel) {
                        if (($channel['type'] ?? 0) === 0) {
                            $channels[] = [
                                'id' => $channel['id'],
                                'name' => $channel['name'],
                            ];
                        }
                    }
                    usort($channels, fn($a, $b) => strcmp($a['name'], $b['name']));
                }

                // Fetch roles using Bot token
                $rolesResponse = Http::withHeaders([
                    'Authorization' => "Bot {$botToken}",
                ])->timeout(5)->get("https://discord.com/api/guilds/{$selectedGuildId}/roles");
                
                if ($rolesResponse->successful()) {
                    $allRoles = $rolesResponse->json();
                    foreach ($allRoles as $role) {
                        if ($role['name'] !== '@everyone') {
                            $roles[] = [
                                'id' => $role['id'],
                                'name' => $role['name'],
                            ];
                        }
                    }
                    usort($roles, fn($a, $b) => strcmp($a['name'], $b['name']));
                }
            } catch (\Exception $e) {
                // Failed to fetch from Discord API
            }
        }

        return view('dashboard.settings', [
            'guilds' => $guilds,
            'selectedGuildId' => $selectedGuildId,
            'currentSettings' => $currentSettings,
            'channels' => $channels,
            'roles' => $roles,
        ]);
    }

    /**
     * Update Settings
     */
    public function updateSettings(Request $request)
    {
        $guildId = session('selected_guild_id');

        if (!$guildId) {
            return redirect()->back()->with('error', 'No server selected');
        }

        $validated = $request->validate([
            'welcome_channel_id' => 'nullable|string',
            'leave_channel_id' => 'nullable|string',
            'log_channel_id' => 'nullable|string',
            'auto_role_id' => 'nullable|string',
            'news_channel_id' => 'nullable|string',
            'game_source_channel_id' => 'nullable|string',
            'request_channel_id' => 'nullable|string',
            'general_chat_channel_id' => 'nullable|string',
        ]);

        DB::table('guild_settings')->updateOrInsert(
            ['guild_id' => $guildId],
            $validated
        );

        return redirect()->back()->with('success', '✅ Settings saved successfully!');
    }
}
