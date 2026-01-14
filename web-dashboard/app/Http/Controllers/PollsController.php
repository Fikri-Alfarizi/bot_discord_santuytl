<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class PollsController extends Controller
{
    public function index()
    {
        $selectedGuildId = session('selected_guild_id');
        $botToken = env('DISCORD_BOT_TOKEN');
        
        $channels = [];
        
        if ($selectedGuildId && $botToken) {
            try {
                $response = Http::withHeaders([
                    'Authorization' => "Bot {$botToken}",
                ])->get("https://discord.com/api/guilds/{$selectedGuildId}/channels");

                if ($response->successful()) {
                    $allChannels = $response->json();
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
            } catch (\Exception $e) { }
        }

        return view('polls.index', compact('channels'));
    }

    public function store(Request $request)
    {
        $guildId = session('selected_guild_id');
        
        $request->validate([
            'channel_id' => 'required|string',
            'question' => 'required|string|max:256',
            'options' => 'required|string', // Newline separated
            'color' => 'nullable|string',
        ]);

        $optionsList = array_map('trim', explode("\n", $request->options));
        $optionsList = array_filter($optionsList); // Remove empty lines
        
        if (count($optionsList) < 2) {
            return redirect()->back()->with('error', 'Please provide at least 2 options.');
        }
        if (count($optionsList) > 5) {
            return redirect()->back()->with('error', 'Maximum 5 options allowed.');
        }

        $payload = [
            'question' => $request->question,
            'options' => array_values($optionsList),
            'color' => $request->color ?? '#6c63ff',
        ];

        DB::table('bot_outbox')->insert([
            'guild_id' => $guildId,
            'channel_id' => $request->channel_id,
            'type' => 'poll_create',
            'payload' => json_encode($payload),
            'processed' => false,
            'created_at' => now(),
        ]);

        return redirect()->back()->with('success', 'Poll created and queued for deployment!');
    }
}
