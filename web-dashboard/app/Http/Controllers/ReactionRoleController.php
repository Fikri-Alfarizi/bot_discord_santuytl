<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class ReactionRoleController extends Controller
{
    public function index()
    {
        $selectedGuildId = session('selected_guild_id');
        $botToken = env('DISCORD_BOT_TOKEN');

        // Fetch existing reaction roles
        $reactionRoles = DB::table('reaction_roles')
            ->where('guild_id', $selectedGuildId)
            ->get();

        // Fetch channels & roles from Discord
        $channels = [];
        $roles = [];

        if ($selectedGuildId && $botToken) {
            try {
                // Channels
                $channelsResponse = Http::withHeaders(['Authorization' => "Bot {$botToken}"])
                    ->timeout(5)->get("https://discord.com/api/guilds/{$selectedGuildId}/channels");
                
                if ($channelsResponse->successful()) {
                    foreach ($channelsResponse->json() as $channel) {
                        if (($channel['type'] ?? 0) === 0) { // Text Channels
                            $channels[] = ['id' => $channel['id'], 'name' => $channel['name']];
                        }
                    }
                    usort($channels, fn($a, $b) => strcmp($a['name'], $b['name']));
                }

                // Roles
                $rolesResponse = Http::withHeaders(['Authorization' => "Bot {$botToken}"])
                    ->timeout(5)->get("https://discord.com/api/guilds/{$selectedGuildId}/roles");

                if ($rolesResponse->successful()) {
                    foreach ($rolesResponse->json() as $role) {
                        if ($role['name'] !== '@everyone') {
                            $roles[] = ['id' => $role['id'], 'name' => $role['name'], 'color' => $role['color']];
                        }
                    }
                    usort($roles, fn($a, $b) => strcmp($a['name'], $b['name']));
                }
            } catch (\Exception $e) {
                // Silent fail
            }
        }

        return view('reaction-roles.index', compact('reactionRoles', 'channels', 'roles'));
    }

    public function store(Request $request)
    {
        $selectedGuildId = session('selected_guild_id');
        
        $request->validate([
            'channel_id' => 'required|string',
            'message_id' => 'required|string',
            'role_id' => 'required|string',
            'emoji' => 'required|string',
        ]);

        // Basic Emoji Validation (Check if it's a standard unicode or custom emoji ID)
        // For simplicity, we accept the string input.
        
        DB::table('reaction_roles')->insert([
            'guild_id' => $selectedGuildId,
            'channel_id' => $request->channel_id,
            'message_id' => $request->message_id,
            'role_id' => $request->role_id,
            'emoji' => $request->emoji,
            'created_at' => now()->timestamp
        ]);

        return redirect()->back()->with('success', 'Reaction Role added successfully!');
    }

    public function destroy($id)
    {
        $selectedGuildId = session('selected_guild_id');
        
        DB::table('reaction_roles')
            ->where('id', $id)
            ->where('guild_id', $selectedGuildId)
            ->delete();

        return redirect()->back()->with('success', 'Reaction Role deleted!');
    }
}
