<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class HubController extends Controller
{
    // Configuration from shop.items.js
    private const SHOP_ITEMS = [
        [
            'id' => 'role_vip',
            'name' => '👑 VIP Role (7 Hari)',
            'description' => 'Dapet role VIP selama 7 hari!',
            'price' => 5000,
            'icon' => 'fa-crown',
            'color' => '#fbbf24',
            'duration' => 604800000
        ],
        [
            'id' => 'color_custom',
            'name' => '🎨 Custom Color',
            'description' => 'Pilih warna sendiri untuk role kamu!',
            'price' => 3000,
            'icon' => 'fa-palette',
            'color' => '#f472b6'
        ],
        [
            'id' => 'xp_boost',
            'name' => '⚡ Double XP (24h)',
            'description' => 'XP kamu jadi 2x lipat selama 24 jam!',
            'price' => 2000,
            'icon' => 'fa-bolt',
            'color' => '#60a5fa',
            'duration' => 86400000
        ],
        [
            'id' => 'coin_boost',
            'name' => '💰 Double Coins (24h)',
            'description' => 'Coins kamu jadi 2x lipat selama 24 jam!',
            'price' => 2000,
            'icon' => 'fa-coins',
            'color' => '#fbbf24',
            'duration' => 86400000
        ],
        [
            'id' => 'rename_bot',
            'name' => '🤖 Rename Bot (1h)',
            'description' => 'Ganti nama bot sesukamu (1 jam)',
            'price' => 10000,
            'icon' => 'fa-robot',
            'color' => '#a78bfa',
            'duration' => 3600000
        ]
    ];

    public function index()
    {
        $user = Auth::user();
        
        // Fetch game stats from 'users' table using discord_id
        $gameStats = DB::table('users')->where('id', $user->discord_id)->first();
        
        // Merge stats into user object for view compatibility
        $user->xp = $gameStats->xp ?? 0;
        $user->level = $gameStats->level ?? 1;
        $user->coins = $gameStats->coins ?? 0;
        $user->last_daily = $gameStats->last_daily ?? 0; // Needed for daily button
        
        // Fetch top events or news
        $news = [
            ['title' => 'Santuy Festival', 'desc' => 'Join our summer festival!', 'color' => 'var(--cute-pink)', 'icon' => 'fa-calendar-star'],
            ['title' => 'Double XP Weekend', 'desc' => 'Get 2x XP on voice channels.', 'color' => 'var(--cute-purple)', 'icon' => 'fa-arrow-up-right-dots'],
        ];

        return view('member.hub', compact('user', 'news'));
    }

    public function market()
    {
        $items = self::SHOP_ITEMS;
        $user = Auth::user();
        $userCoins = DB::table('users')->where('id', $user->discord_id)->value('coins') ?? 0;
        
        return view('member.market', compact('items', 'userCoins'));
    }

    public function leaderboard()
    {
        // 1. Get Top 10 Users from Game Stats
        $topUsers = DB::table('users')
            ->orderBy('xp', 'desc')
            ->limit(10)
            ->get();
            
        // 2. Get Web User Data (Avatar/Name) manually to avoid SQL Collation Error
        $discordIds = $topUsers->pluck('id')->toArray();
        
        $webUsers = [];
        if (!empty($discordIds)) {
            $webUsers = DB::table('web_users')
                ->whereIn('discord_id', $discordIds)
                ->get()
                ->keyBy('discord_id');
        }
            
        // 3. Merge Data
        foreach ($topUsers as $u) {
            $webUser = isset($webUsers[$u->id]) ? $webUsers[$u->id] : null;
            
            if ($webUser && $webUser->avatar) {
                $u->avatar = $webUser->avatar;
                $u->web_name = $webUser->name;
            } else {
                // Fetch from Discord API if not in web_users (Cached)
                $discordData = Cache::remember('discord_user_'.$u->id, 86400, function () use ($u) {
                    try {
                        $response = Http::withHeaders([
                            'Authorization' => 'Bot ' . env('DISCORD_BOT_TOKEN')
                        ])->get("https://discord.com/api/v10/users/{$u->id}");
                        
                        if ($response->successful()) {
                            $data = $response->json();
                            $avatar = $data['avatar'] 
                                ? "https://cdn.discordapp.com/avatars/{$u->id}/{$data['avatar']}.png" 
                                : "https://cdn.discordapp.com/embed/avatars/".(($data['discriminator'] ?? 0) % 5).".png";
                            
                            return [
                                'avatar' => $avatar,
                                'username' => $data['global_name'] ?? $data['username'],
                            ];
                        }
                    } catch (\Exception $e) {
                        // Ignore errors
                    }
                    return null;
                });

                $u->avatar = $discordData['avatar'] ?? 'https://cdn.discordapp.com/embed/avatars/0.png';
                $u->web_name = $discordData['username'] ?? $u->username;
            }
        }

        return view('member.leaderboard', compact('topUsers'));
    }

    public function inventory()
    {
        $user = Auth::user();
        $inventoryItems = DB::table('inventory')
            ->where('user_id', $user->discord_id)
            ->get();

        // Enrich inventory data with item details from constant
        $items = [];
        $allItems = collect(self::SHOP_ITEMS)->keyBy('id');

        foreach ($inventoryItems as $inv) {
            if ($allItems->has($inv->item_id)) {
                $itemDetails = $allItems->get($inv->item_id);
                $items[] = (object) array_merge((array) $inv, $itemDetails);
            } else {
                 // Fallback for unknown items
                $items[] = (object) array_merge((array) $inv, [
                    'name' => 'Unknown Item (' . $inv->item_id . ')',
                    'icon' => 'fa-question',
                    'description' => 'Item data not found.',
                    'color' => '#64748b'
                ]);
            }
        }

        return view('member.inventory', compact('items'));
    }

    public function buy(Request $request)
    {
        $user = Auth::user();
        $itemId = $request->input('item_id');
        
        // Find Item
        $item = collect(self::SHOP_ITEMS)->firstWhere('id', $itemId);
        if (!$item) {
            return response()->json(['success' => false, 'message' => 'Item not found.']);
        }

        DB::beginTransaction();
        try {
            // Check Balance with Lock
            $userData = DB::table('users')->where('id', $user->discord_id)->lockForUpdate()->first();
            
            if ($userData->coins < $item['price']) {
                DB::rollBack();
                return response()->json(['success' => false, 'message' => 'Saldo tidak cukup!']);
            }

            // Deduct Coins
            DB::table('users')->where('id', $user->discord_id)->decrement('coins', $item['price']);

            // Add to Inventory
            DB::table('inventory')->insert([
                'user_id' => $user->discord_id,
                'item_id' => $itemId,
                'quantity' => 1,
                'created_at' => now()->timestamp,
                // Add expires_at if duration exists
                'expires_at' => isset($item['duration']) ? now()->addMilliseconds($item['duration'])->timestamp : null
            ]);

            DB::commit();
            return response()->json(['success' => true, 'message' => "Berhasil membeli {$item['name']}!"]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan sistem: ' . $e->getMessage()]);
        }
    }

    public function daily()
    {
        $user = Auth::user();
        $rewardCoins = 1000;
        $rewardXp = 500;
        $cooldown = 86400; // 24 hours in seconds

        DB::beginTransaction();
        try {
            $userData = DB::table('users')->where('id', $user->discord_id)->lockForUpdate()->first();
            $now = now()->timestamp; // Use timestamp (seconds) since DB usually stores BigInt timestamp (ms? check code)
            
            // Check if DB stores MS or Seconds. Shop uses MS for duration. Init DB uses BIGINT.
            // Bot usually uses MS for timestamps in JS. 
            // src/db/index.js: last_daily BIGINT DEFAULT 0.
            // Let's assume MS as JS standard.
            $nowMs = now()->timestamp * 1000;
            $cooldownMs = $cooldown * 1000;
            
            if ($userData->last_daily && ($nowMs - $userData->last_daily < $cooldownMs)) {
                $remainingSeconds = ($cooldownMs - ($nowMs - $userData->last_daily)) / 1000;
                $hours = floor($remainingSeconds / 3600);
                $minutes = floor(($remainingSeconds % 3600) / 60);
                
                DB::rollBack();
                return response()->json([
                    'success' => false, 
                    'message' => "Tunggu {$hours}j {$minutes}m lagi!"
                ]);
            }

            // Update User
            DB::table('users')->where('id', $user->discord_id)->update([
                'coins' => DB::raw("coins + $rewardCoins"),
                'xp' => DB::raw("xp + $rewardXp"),
                'last_daily' => $nowMs
            ]);

            DB::commit();
            return response()->json([
                'success' => true, 
                'message' => "Daily Claimed! +{$rewardCoins} Coins & +{$rewardXp} XP"
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
    }

    public function profile()
    {
        $user = Auth::user();
        $gameStats = DB::table('users')->where('id', $user->discord_id)->first();
        
        $user->xp = $gameStats->xp ?? 0;
        $user->level = $gameStats->level ?? 1;
        $user->coins = $gameStats->coins ?? 0;
        $user->created_at = $user->created_at; // Ensure this is preserved
        
        return view('member.profile', compact('user'));
    }
}
