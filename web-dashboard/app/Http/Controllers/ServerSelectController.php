<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class ServerSelectController extends Controller
{
    /**
     * Show server selection page
     */
    public function index()
    {
        $guilds = session('discord_guilds', []);

        if (empty($guilds)) {
            return redirect('/')->with('error', 'No servers found. Please login again.');
        }

        // Fetch Bot Guilds to check which servers the bot is already in
        // DEBUG MODE: Cache removed, dumping info
        $token = env('DISCORD_BOT_TOKEN');
        
        if (!$token) {
            dd("ERROR: DISCORD_BOT_TOKEN is missing from web-dashboard/.env file.");
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bot ' . $token,
            ])->get('https://discord.com/api/users/@me/guilds');

            if (!$response->successful()) {
                dd("Discord API Error: " . $response->status(), $response->body());
            }

            $botGuilds = collect($response->json())->pluck('id')->toArray();
        } catch (\Exception $e) {
            dd("Exception: " . $e->getMessage());
        }

        // Add has_bot status to guilds
        foreach ($guilds as &$guild) {
            $guild['has_bot'] = in_array($guild['id'], $botGuilds);
        }

        return view('select-server', compact('guilds'));
    }

    /**
     * Select a server
     */
    public function select(Request $request)
    {
        $guildId = $request->input('guild_id');
        $guilds = session('discord_guilds', []);

        // Verify guild exists in user's list
        $selectedGuild = collect($guilds)->firstWhere('id', $guildId);

        if (!$selectedGuild) {
            return redirect()->back()->with('error', 'Server not found');
        }

        session(['selected_guild_id' => $guildId, 'selected_guild' => $selectedGuild]);

        return redirect()->route('dashboard', ['guildId' => $guildId]);
    }
}
