<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class EmojisController extends Controller
{
    public function index()
    {
        $guildId = session('selected_guild_id');
        $botToken = config('services.discord.bot_token');

        // Fetch Emojis from Discord API
        $response = Http::withHeaders([
            'Authorization' => 'Bot ' . $botToken,
        ])->get("https://discord.com/api/v10/guilds/{$guildId}/emojis");

        $emojis = [];
        if ($response->successful()) {
            $emojis = $response->json();
        }

        return view('emojis.index', compact('emojis'));
    }
}
