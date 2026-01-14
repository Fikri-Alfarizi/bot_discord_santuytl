<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class TempChannelsController extends Controller
{
    public function index()
    {
        $guildId = session('selected_guild_id');
        $botToken = env('DISCORD_BOT_TOKEN');
        
        $config = DB::table('temp_channel_configs')->where('guild_id', $guildId)->first();
        
        $channels = [];
        $categories = [];
        
        if ($guildId && $botToken) {
            try {
                $response = Http::withHeaders([
                    'Authorization' => "Bot {$botToken}",
                ])->get("https://discord.com/api/guilds/{$guildId}/channels");

                if ($response->successful()) {
                    $allChannels = $response->json();
                    foreach ($allChannels as $channel) {
                        // Type 2 = Voice Channel, Type 4 = Category
                        if (($channel['type'] ?? 0) === 2) {
                            $channels[] = [
                                'id' => $channel['id'],
                                'name' => $channel['name'],
                            ];
                        }
                        if (($channel['type'] ?? 0) === 4) {
                            $categories[] = [
                                'id' => $channel['id'],
                                'name' => $channel['name'],
                            ];
                        }
                    }
                    usort($channels, fn($a, $b) => strcmp($a['name'], $b['name']));
                    usort($categories, fn($a, $b) => strcmp($a['name'], $b['name']));
                }
            } catch (\Exception $e) { }
        }

        return view('temp-channels.index', compact('config', 'channels', 'categories'));
    }

    public function store(Request $request)
    {
        $guildId = session('selected_guild_id');
        
        $request->validate([
            'hub_channel_id' => 'required|string',
            'category_id' => 'required|string',
            'default_name' => 'required|string|max:100',
        ]);

        DB::table('temp_channel_configs')->updateOrInsert(
            ['guild_id' => $guildId],
            [
                'hub_channel_id' => $request->hub_channel_id,
                'category_id' => $request->category_id,
                'default_name' => $request->default_name,
                'updated_at' => now(),
            ]
        );

        return redirect()->back()->with('success', 'Configuration saved.');
    }
}
