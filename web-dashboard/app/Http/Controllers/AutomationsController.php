<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class AutomationsController extends Controller
{
    public function index()
    {
        $guildId = session('selected_guild_id');
        $botToken = env('DISCORD_BOT_TOKEN');
        
        $automations = DB::table('automations')->where('guild_id', $guildId)->orderBy('created_at', 'desc')->get();
        
        $channels = [];
        $roles = [];

        if ($guildId && $botToken) {
            try {
                // Fetch Channels
                $response = Http::withHeaders(['Authorization' => "Bot {$botToken}"])->get("https://discord.com/api/guilds/{$guildId}/channels");
                if ($response->successful()) {
                    foreach ($response->json() as $channel) {
                        // Voice channels (2)
                        if (($channel['type'] ?? 0) === 2) {
                            $channels[] = ['id' => $channel['id'], 'name' => $channel['name']];
                        }
                    }
                    usort($channels, fn($a, $b) => strcmp($a['name'], $b['name']));
                }

                // Fetch Roles
                $roleResponse = Http::withHeaders(['Authorization' => "Bot {$botToken}"])->get("https://discord.com/api/guilds/{$guildId}/roles");
                if ($roleResponse->successful()) {
                    foreach ($roleResponse->json() as $role) {
                        if ($role['name'] !== '@everyone' && !$role['managed']) {
                            $roles[] = ['id' => $role['id'], 'name' => $role['name'], 'color' => $role['color']];
                        }
                    }
                    usort($roles, fn($a, $b) => strcmp($a['name'], $b['name']));
                }
            } catch (\Exception $e) { }
        }

        return view('automations.index', compact('automations', 'channels', 'roles'));
    }

    public function store(Request $request)
    {
        $guildId = session('selected_guild_id');
        
        $request->validate([
            'name' => 'required|string|max:50',
            'event' => 'required|string',
            'action_type' => 'required|string',
        ]);

        DB::table('automations')->insert([
            'guild_id' => $guildId,
            'name' => $request->name,
            'event' => $request->event,
            'trigger_value' => $request->trigger_value,
            'action_type' => $request->action_type,
            'action_value' => $request->action_value,
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->back()->with('success', 'Automation created.');
    }

    public function destroy($id)
    {
        DB::table('automations')->where('id', $id)->delete();
        return redirect()->back()->with('success', 'Automation deleted.');
    }

    public function toggle($id)
    {
        $auto = DB::table('automations')->where('id', $id)->first();
        if ($auto) {
            DB::table('automations')->where('id', $id)->update(['is_active' => !$auto->is_active]);
        }
        return redirect()->back()->with('success', 'Status updated.');
    }
}
