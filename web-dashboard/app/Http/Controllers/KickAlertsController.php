<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class KickAlertsController extends Controller
{
    public function index()
    {
        $guildId = session('selected_guild_id');
        $botToken = env('DISCORD_BOT_TOKEN');
        
        $alerts = DB::table('social_alerts')
            ->where('guild_id', $guildId)
            ->where('platform', 'kick')
            ->get();
        
        $channels = $this->getDiscordChannels($guildId, $botToken);

        return view('social.kick.index', compact('alerts', 'channels'));
    }

    public function store(Request $request)
    {
        $guildId = session('selected_guild_id');
        
        $request->validate([
            'identifier' => 'required|string',
            'discord_channel_id' => 'required|string',
        ]);

        DB::table('social_alerts')->insert([
            'guild_id' => $guildId,
            'platform' => 'kick',
            'identifier' => $request->identifier,
            'discord_channel_id' => $request->discord_channel_id,
            'message' => $request->message ?? "{identifier} is now live on Kick! {link}",
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->back()->with('success', 'Kick Alert created.');
    }

    public function destroy($id)
    {
        DB::table('social_alerts')->where('id', $id)->delete();
        return redirect()->back()->with('success', 'Alert deleted.');
    }

    private function getDiscordChannels($guildId, $botToken) {
        $channels = [];
        if ($guildId && $botToken) {
            try {
                $response = Http::withHeaders(['Authorization' => "Bot {$botToken}"])
                    ->get("https://discord.com/api/guilds/{$guildId}/channels");
                if ($response->successful()) {
                    foreach ($response->json() as $channel) {
                        if (($channel['type'] ?? 0) === 0 || ($channel['type'] ?? 0) === 5) {
                            $channels[] = ['id' => $channel['id'], 'name' => $channel['name']];
                        }
                    }
                    usort($channels, fn($a, $b) => strcmp($a['name'], $b['name']));
                }
            } catch (\Exception $e) { }
        }
        return $channels;
    }
}
