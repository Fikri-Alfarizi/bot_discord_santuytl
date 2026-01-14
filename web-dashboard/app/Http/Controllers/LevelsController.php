<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class LevelsController extends Controller
{
    private $discordApiUrl = 'https://discord.com/api/v10';

    public function index(Request $request, $guildId)
    {
        $rewards = DB::table('level_rewards')
            ->where('guild_id', $guildId)
            ->orderBy('level', 'asc')
            ->get();
        
        $roles = [];
        $botToken = env('DISCORD_BOT_TOKEN');
        
        if ($botToken) {
            $response = Http::withHeaders([
                'Authorization' => 'Bot ' . $botToken,
            ])->get("{$this->discordApiUrl}/guilds/{$guildId}/roles");

            if ($response->successful()) {
                $roles = $response->json();
                $roles = array_filter($roles, function($role) {
                    return $role['name'] !== '@everyone' && !$role['managed'];
                });
                
                usort($roles, function($a, $b) {
                    return $b['position'] <=> $a['position'];
                });
            }
        }

        return view('levels.index', compact('rewards', 'roles', 'guildId'));
    }

    public function store(Request $request, $guildId)
    {
        $request->validate([
            'level' => 'required|integer|min:1',
            'role_id' => 'required|string',
        ]);

        $exists = DB::table('level_rewards')
            ->where('guild_id', $guildId)
            ->where('level', $request->level)
            ->exists();

        if ($exists) {
            return back()->with('error', 'Reward untuk level ini sudah ada. Hapus dulu jika ingin mengubah.');
        }

        DB::table('level_rewards')->insert([
            'guild_id' => $guildId,
            'level' => $request->level,
            'role_id' => $request->role_id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return back()->with('success', 'Level reward berhasil ditambahkan.');
    }

    public function destroy($guildId, $id)
    {
        DB::table('level_rewards')
            ->where('guild_id', $guildId)
            ->where('id', $id)
            ->delete();

        return back()->with('success', 'Level reward dihapus.');
    }
}
