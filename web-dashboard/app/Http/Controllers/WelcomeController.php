<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class WelcomeController extends Controller
{
    /**
     * Welcome & Goodbye configuration page
     */
    /**
     * Welcome & Goodbye configuration page
     */
    public function index(string $guildId)
    {
        $selectedGuildId = $guildId; // Use route param
        $botToken = env('DISCORD_BOT_TOKEN');
        
        // Get current settings
        $settings = DB::table('guild_settings')
            ->where('guild_id', $selectedGuildId)
            ->first();

        // Fetch channels
        $channels = [];
        $roles = [];
        
        if ($selectedGuildId && $botToken) {
            try {
                $channelsResponse = Http::withHeaders([
                    'Authorization' => "Bot {$botToken}",
                ])->timeout(5)->get("https://discord.com/api/guilds/{$selectedGuildId}/channels");
                
                if ($channelsResponse->successful()) {
                    foreach ($channelsResponse->json() as $channel) {
                        if (($channel['type'] ?? 0) === 0) {
                            $channels[] = ['id' => $channel['id'], 'name' => $channel['name']];
                        }
                    }
                    usort($channels, fn($a, $b) => strcmp($a['name'], $b['name']));
                }

                $rolesResponse = Http::withHeaders([
                    'Authorization' => "Bot {$botToken}",
                ])->timeout(5)->get("https://discord.com/api/guilds/{$selectedGuildId}/roles");
                
                if ($rolesResponse->successful()) {
                    foreach ($rolesResponse->json() as $role) {
                        if ($role['name'] !== '@everyone') {
                            $roles[] = ['id' => $role['id'], 'name' => $role['name']];
                        }
                    }
                    usort($roles, fn($a, $b) => strcmp($a['name'], $b['name']));
                }
            } catch (\Exception $e) {
                // Silent fail
            }
        }

        return view('welcome-config.index', compact('settings', 'channels', 'roles', 'selectedGuildId'));
    }

    /**
     * Save welcome settings
     */
    public function update(Request $request, string $guildId)
    {
        $selectedGuildId = $guildId; // Use route param

        $validated = $request->validate([
            'welcome_enabled' => 'nullable|boolean',
            'welcome_channel_id' => 'nullable|string',
            'welcome_message' => 'nullable|string|max:2000',
            'welcome_embed_enabled' => 'nullable|boolean',
            'welcome_embed_title' => 'nullable|string|max:256',
            'welcome_embed_description' => 'nullable|string|max:4096',
            'welcome_embed_color' => 'nullable|string',
            'welcome_embed_image' => 'nullable|url',
            'welcome_embed_thumbnail' => 'nullable|url',
            'welcome_dm_enabled' => 'nullable|boolean',
            'welcome_dm_message' => 'nullable|string|max:2000',
            
            'goodbye_enabled' => 'nullable|boolean',
            'goodbye_channel_id' => 'nullable|string',
            'goodbye_message' => 'nullable|string|max:2000',
            'goodbye_embed_enabled' => 'nullable|boolean',
            'goodbye_embed_title' => 'nullable|string|max:256',
            'goodbye_embed_description' => 'nullable|string|max:4096',
            'goodbye_embed_color' => 'nullable|string',
            
            'auto_role_enabled' => 'nullable|boolean',
            'auto_role_id' => 'nullable|string',
        ]);

        // Convert checkbox values
        $data = [];
        foreach ($validated as $key => $value) {
            if (str_ends_with($key, '_enabled')) {
                $data[$key] = $request->has($key) ? 1 : 0;
            } else {
                $data[$key] = $value;
            }
        }

        DB::table('guild_settings')->updateOrInsert(
            ['guild_id' => $selectedGuildId],
            $data
        );

        return redirect()->back()->with('success', '✅ Welcome & Goodbye settings saved!');
    }

    /**
     * Test welcome message
     */
    public function test(Request $request, string $guildId)
    {
        $selectedGuildId = $guildId; // Use route param
        $botToken = env('DISCORD_BOT_TOKEN');
        $channelId = $request->input('channel_id');
        $type = $request->input('type', 'welcome');

        $settings = DB::table('guild_settings')
            ->where('guild_id', $selectedGuildId)
            ->first();

        if (!$settings || !$channelId || !$botToken) {
            return redirect()->back()->with('error', 'Missing configuration');
        }

        // Build message
        $user = auth()->user();
        $testMessage = $type === 'welcome' 
            ? ($settings->welcome_message ?? 'Welcome {user} to {server}!')
            : ($settings->goodbye_message ?? 'Goodbye {user}!');

        // Replace placeholders
        $testMessage = str_replace(
            ['{user}', '{username}', '{server}', '{membercount}'],
            ["<@{$user->discord_id}>", $user->name, 'Test Server', '100'],
            $testMessage
        );

        $payload = ['content' => $testMessage];

        // Add embed if enabled
        $embedEnabled = $type === 'welcome' 
            ? ($settings->welcome_embed_enabled ?? false)
            : ($settings->goodbye_embed_enabled ?? false);

        if ($embedEnabled) {
            $embed = [];
            
            if ($type === 'welcome') {
                if ($settings->welcome_embed_title) $embed['title'] = $settings->welcome_embed_title;
                if ($settings->welcome_embed_description) {
                    $embed['description'] = str_replace(
                        ['{user}', '{username}', '{server}', '{membercount}'],
                        ["<@{$user->discord_id}>", $user->name, 'Test Server', '100'],
                        $settings->welcome_embed_description
                    );
                }
                if ($settings->welcome_embed_color) $embed['color'] = hexdec(ltrim($settings->welcome_embed_color, '#'));
                if ($settings->welcome_embed_image) $embed['image'] = ['url' => $settings->welcome_embed_image];
                if ($settings->welcome_embed_thumbnail) $embed['thumbnail'] = ['url' => $settings->welcome_embed_thumbnail];
            }

            if (!empty($embed)) {
                $embed['timestamp'] = now()->toIso8601String();
                $payload['embeds'] = [$embed];
            }
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => "Bot {$botToken}",
                'Content-Type' => 'application/json',
            ])->post("https://discord.com/api/channels/{$channelId}/messages", $payload);

            if ($response->successful()) {
                return redirect()->back()->with('success', '✅ Test message sent!');
            } else {
                return redirect()->back()->with('error', 'Failed to send: ' . $response->body());
            }
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error: ' . $e->getMessage());
        }
    }
}
