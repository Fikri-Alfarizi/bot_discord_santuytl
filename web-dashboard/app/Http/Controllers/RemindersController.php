<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;

class RemindersController extends Controller
{
    public function index()
    {
        $guildId = session('selected_guild_id');
        $botToken = env('DISCORD_BOT_TOKEN');
        
        $reminders = DB::table('reminders')->where('guild_id', $guildId)->get();
        
        $channels = [];
        if ($guildId && $botToken) {
            try {
                $response = Http::withHeaders([
                    'Authorization' => "Bot {$botToken}",
                ])->get("https://discord.com/api/guilds/{$guildId}/channels");

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

        return view('reminders.index', compact('reminders', 'channels'));
    }

    public function store(Request $request)
    {
        $guildId = session('selected_guild_id');
        
        $request->validate([
            'channel_id' => 'required|string',
            'message' => 'required|string|max:2000',
            'interval_minutes' => 'required|integer|min:1',
        ]);

        DB::table('reminders')->insert([
            'guild_id' => $guildId,
            'channel_id' => $request->channel_id,
            'message' => $request->message,
            'interval_minutes' => $request->interval_minutes,
            'next_run_at' => Carbon::now(), // Start immediately (on next cron tick)
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->back()->with('success', 'Reminder created.');
    }

    public function destroy($id)
    {
        DB::table('reminders')->where('id', $id)->delete();
        return redirect()->back()->with('success', 'Reminder deleted.');
    }
}
