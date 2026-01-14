<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;

class GiveawaysController extends Controller
{
    public function index()
    {
        $guildId = session('selected_guild_id');
        $botToken = env('DISCORD_BOT_TOKEN');
        
        $giveaways = DB::table('giveaways')
            ->where('guild_id', $guildId)
            ->orderBy('created_at', 'desc')
            ->get();
            
        // Add participant count
        foreach ($giveaways as $g) {
            $g->participant_count = DB::table('giveaway_participants')->where('giveaway_id', $g->id)->count();
        }
        
        $channels = [];
        if ($guildId && $botToken) {
            try {
                $response = Http::withHeaders(['Authorization' => "Bot {$botToken}"])->get("https://discord.com/api/guilds/{$guildId}/channels");
                if ($response->successful()) {
                    foreach ($response->json() as $channel) {
                        // Text channels (0) or News (5)
                        if (($channel['type'] ?? 0) === 0 || ($channel['type'] ?? 0) === 5) {
                            $channels[] = ['id' => $channel['id'], 'name' => $channel['name']];
                        }
                    }
                    usort($channels, fn($a, $b) => strcmp($a['name'], $b['name']));
                }
            } catch (\Exception $e) { }
        }

        return view('giveaways.index', compact('giveaways', 'channels'));
    }

    public function store(Request $request)
    {
        $guildId = session('selected_guild_id');
        
        $request->validate([
            'prize' => 'required|string|max:100',
            'winner_count' => 'required|integer|min:1',
            'duration' => 'required|integer|min:1',
            'duration_unit' => 'required|in:minutes,hours,days',
            'channel_id' => 'required|string',
        ]);

        $endAt = now();
        if ($request->duration_unit === 'minutes') $endAt->addMinutes($request->duration);
        elseif ($request->duration_unit === 'hours') $endAt->addHours($request->duration);
        elseif ($request->duration_unit === 'days') $endAt->addDays($request->duration);

        $giveawayId = DB::table('giveaways')->insertGetId([
            'guild_id' => $guildId,
            'channel_id' => $request->channel_id,
            'prize' => $request->prize,
            'description' => $request->description,
            'winner_count' => $request->winner_count,
            'end_at' => $endAt,
            'status' => 'active',
            'host_id' => null, // Can't easily get user ID from session here without OAuth user object check
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Send to Bot Outbox
        DB::table('bot_outbox')->insert([
            'guild_id' => $guildId,
            'action' => 'create_giveaway',
            'payload' => json_encode([
                'giveaway_id' => $giveawayId,
                'channel_id' => $request->channel_id,
                'prize' => $request->prize,
                'description' => $request->description,
                'winner_count' => $request->winner_count,
                'end_at' => $endAt->timestamp * 1000, // JS uses ms
            ]),
            'status' => 'pending',
            'created_at' => now(),
        ]);

        return redirect()->back()->with('success', 'Giveaway created! Bot will post it shortly.');
    }

    public function destroy($id)
    {
        // Ideally we should also tell bot to edit the message to say "Cancelled"
        // For now just delete from DB. Bot cron will ignore it since it's gone.
        DB::table('giveaways')->where('id', $id)->delete();
        return redirect()->back()->with('success', 'Giveaway deleted.');
    }
}
