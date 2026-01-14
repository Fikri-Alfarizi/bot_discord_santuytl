<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;

class BirthdaysController extends Controller
{
    public function index()
    {
        $guildId = session('selected_guild_id');
        $botToken = env('DISCORD_BOT_TOKEN');
        
        $settings = DB::table('guild_settings')->where('guild_id', $guildId)->first();
        $birthdays = DB::table('birthdays')
            ->where('guild_id', $guildId)
            ->orderBy('month', 'asc')
            ->orderBy('day', 'asc')
            ->get();
            
        // Sort by upcoming
        $sortedBirthdays = $birthdays->sortBy(function ($b) {
            $now = Carbon::now();
            $bDate = Carbon::createFromDate($now->year, $b->month, $b->day);
            if ($bDate->isPast() && !$bDate->isToday()) {
                $bDate->addYear();
            }
            return $bDate->timestamp;
        });

        $channels = [];
        if ($guildId && $botToken) {
            try {
                $response = Http::withHeaders(['Authorization' => "Bot {$botToken}"])->get("https://discord.com/api/guilds/{$guildId}/channels");
                if ($response->successful()) {
                    foreach ($response->json() as $channel) {
                        if (($channel['type'] ?? 0) === 0 || ($channel['type'] ?? 0) === 5) { // Text or News
                            $channels[] = ['id' => $channel['id'], 'name' => $channel['name']];
                        }
                    }
                    usort($channels, fn($a, $b) => strcmp($a['name'], $b['name']));
                }
            } catch (\Exception $e) { }
        }

        return view('birthdays.index', [
            'settings' => $settings,
            'birthdays' => $sortedBirthdays,
            'channels' => $channels
        ]);
    }

    public function update(Request $request)
    {
        $guildId = session('selected_guild_id');
        
        $request->validate([
            'birthday_channel_id' => 'required|string',
            'birthday_message' => 'required|string|max:500',
        ]);

        DB::table('guild_settings')->updateOrInsert(
            ['guild_id' => $guildId],
            [
                'birthday_channel_id' => $request->birthday_channel_id,
                'birthday_message' => $request->birthday_message,
                'updated_at' => now(),
            ]
        );

        return redirect()->back()->with('success', 'Birthday settings updated.');
    }
    
    // Admin manually delete birthday if needed
    public function destroy($id)
    {
        DB::table('birthdays')->where('id', $id)->delete();
        return redirect()->back()->with('success', 'Birthday entry deleted.');
    }
}
