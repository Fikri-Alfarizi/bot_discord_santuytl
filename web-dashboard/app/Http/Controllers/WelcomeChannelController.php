<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class WelcomeChannelController extends Controller
{
    public function index(string $guildId)
    {
        $selectedGuildId = $guildId;
        $botToken = env('DISCORD_BOT_TOKEN');

        // Fetch existing config
        $welcomeInfo = DB::table('welcome_infos')->where('guild_id', $selectedGuildId)->first();
        
        // Decode embed data if exists
        $embedData = $welcomeInfo && $welcomeInfo->embed_data ? json_decode($welcomeInfo->embed_data, true) : null;

        // Fetch channels from Discord
        $channels = [];
        if ($selectedGuildId && $botToken) {
            try {
                $response = Http::withHeaders(['Authorization' => "Bot {$botToken}"])
                    ->get("https://discord.com/api/guilds/{$selectedGuildId}/channels");
                
                if ($response->successful()) {
                    $allChannels = $response->json();
                    foreach ($allChannels as $channel) {
                        // Type 0 is text channel
                        if (isset($channel['type']) && $channel['type'] === 0) {
                            $channels[] = ['id' => $channel['id'], 'name' => $channel['name']];
                        }
                    }
                    usort($channels, fn($a, $b) => strcmp($a['name'], $b['name']));
                }
            } catch (\Exception $e) {
                // Ignore
            }
        }

        return view('welcome-channel.index', compact('welcomeInfo', 'embedData', 'channels'));
    }

    public function store(Request $request, string $guildId)
    {
        $selectedGuildId = $guildId;
        
        $request->validate([
            'channel_id' => 'required|string',
            'message_content' => 'nullable|string|max:2000',
            'embed_title' => 'nullable|string|max:256',
            'embed_description' => 'nullable|string|max:4096',
            'embed_color' => 'nullable|string',
            'embed_image' => 'nullable|url',
            'embed_thumbnail' => 'nullable|url',
            'embed_author_name' => 'nullable|string|max:256',
            'embed_author_icon' => 'nullable|url',
            'embed_footer_text' => 'nullable|string|max:2048',
            'add_timestamp' => 'nullable|boolean',
        ]);

        $embedData = [
            'title' => $request->embed_title,
            'description' => $request->embed_description,
            'color' => $request->embed_color,
            'image' => $request->embed_image,
            'thumbnail' => $request->embed_thumbnail,
            'author' => [
                'name' => $request->embed_author_name,
                'icon_url' => $request->embed_author_icon,
            ],
            'footer' => [
                'text' => $request->embed_footer_text,
            ],
            'timestamp' => $request->has('add_timestamp'),
        ];

        DB::table('welcome_infos')->updateOrInsert(
            ['guild_id' => $selectedGuildId],
            [
                'channel_id' => $request->channel_id,
                'message_content' => $request->message_content,
                'embed_data' => json_encode($embedData),
                'updated_at' => now(),
            ]
        );

        return redirect()->back()->with('success', 'Configuration saved successfully!');
    }

    public function deploy(string $guildId)
    {
        $selectedGuildId = $guildId;
        $botToken = env('DISCORD_BOT_TOKEN');

        $info = DB::table('welcome_infos')->where('guild_id', $selectedGuildId)->first();

        if (!$info) {
            return redirect()->back()->with('error', 'Please save configuration first.');
        }

        $embedData = json_decode($info->embed_data, true);
        $payload = [];

        if ($info->message_content) {
            $payload['content'] = $info->message_content;
        }

        if ($embedData) {
            $embed = [];
            if (!empty($embedData['title'])) $embed['title'] = $embedData['title'];
            if (!empty($embedData['description'])) $embed['description'] = $embedData['description'];
            if (!empty($embedData['color'])) $embed['color'] = hexdec(ltrim($embedData['color'], '#'));
            if (!empty($embedData['image'])) $embed['image'] = ['url' => $embedData['image']];
            if (!empty($embedData['thumbnail'])) $embed['thumbnail'] = ['url' => $embedData['thumbnail']];
            
            if (!empty($embedData['author']['name'])) {
                $embed['author'] = ['name' => $embedData['author']['name']];
                if (!empty($embedData['author']['icon_url'])) {
                    $embed['author']['icon_url'] = $embedData['author']['icon_url'];
                }
            }

            if (!empty($embedData['footer']['text'])) {
                $embed['footer'] = ['text' => $embedData['footer']['text']];
            }

            if (!empty($embedData['timestamp'])) {
                $embed['timestamp'] = now()->toIso8601String();
            }
            
            if (!empty($embed)) {
                $payload['embeds'] = [$embed];
            }
        }

        if (empty($payload)) {
            return redirect()->back()->with('error', 'Message cannot be empty.');
        }

        try {
            $response = Http::withHeaders(['Authorization' => "Bot {$botToken}"])
                ->post("https://discord.com/api/channels/{$info->channel_id}/messages", $payload);

            if ($response->successful()) {
                return redirect()->back()->with('success', 'Message deployed to Discord!');
            } else {
                return redirect()->back()->with('error', 'Failed to deploy: ' . $response->body());
            }
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error: ' . $e->getMessage());
        }
    }
}
