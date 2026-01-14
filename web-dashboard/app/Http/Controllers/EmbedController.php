<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class EmbedController extends Controller
{
    /**
     * Show embed builder page
     */
    public function index()
    {
        $selectedGuildId = session('selected_guild_id');
        $botToken = env('DISCORD_BOT_TOKEN');
        
        $channels = [];
        
        if ($selectedGuildId && $botToken) {
            try {
                $response = Http::withHeaders([
                    'Authorization' => "Bot {$botToken}",
                ])->get("https://discord.com/api/guilds/{$selectedGuildId}/channels");

                if ($response->successful()) {
                    $allChannels = $response->json();
                    // Filter text channels only
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
            } catch (\Exception $e) {
                // Silent fail
            }
        }

        return view('embed.index', compact('channels'));
    }

    /**
     * Send embed to Discord channel
     */
    public function send(Request $request)
    {
        $validated = $request->validate([
            'channel_id' => 'required|string',
            'title' => 'nullable|string|max:256',
            'description' => 'nullable|string|max:4096',
            'color' => 'nullable|string',
            'author_name' => 'nullable|string|max:256',
            'author_icon' => 'nullable|url',
            'footer_text' => 'nullable|string|max:2048',
            'footer_icon' => 'nullable|url',
            'thumbnail' => 'nullable|url',
            'image' => 'nullable|url',
            'add_timestamp' => 'nullable|boolean',
            'fields' => 'nullable|array',
            'content' => 'nullable|string|max:2000',
        ]);

        $botToken = env('DISCORD_BOT_TOKEN');

        if (!$botToken) {
            return redirect()->back()->with('error', 'Bot token not configured');
        }

        // Build embed object
        $embed = [];

        if (!empty($validated['title'])) {
            $embed['title'] = $validated['title'];
        }

        if (!empty($validated['description'])) {
            $embed['description'] = $validated['description'];
        }

        if (!empty($validated['color'])) {
            // Convert hex to integer
            $embed['color'] = hexdec(ltrim($validated['color'], '#'));
        }

        if (!empty($validated['author_name'])) {
            $embed['author'] = ['name' => $validated['author_name']];
            if (!empty($validated['author_icon'])) {
                $embed['author']['icon_url'] = $validated['author_icon'];
            }
        }

        if (!empty($validated['footer_text'])) {
            $embed['footer'] = ['text' => $validated['footer_text']];
            if (!empty($validated['footer_icon'])) {
                $embed['footer']['icon_url'] = $validated['footer_icon'];
            }
        }

        if (!empty($validated['thumbnail'])) {
            $embed['thumbnail'] = ['url' => $validated['thumbnail']];
        }

        if (!empty($validated['image'])) {
            $embed['image'] = ['url' => $validated['image']];
        }

        if (!empty($validated['add_timestamp'])) {
            $embed['timestamp'] = now()->toIso8601String();
        }

        if (!empty($validated['fields'])) {
            $embed['fields'] = array_map(function ($field) {
                return [
                    'name' => $field['name'] ?? 'Field',
                    'value' => $field['value'] ?? '',
                    'inline' => $field['inline'] ?? false,
                ];
            }, $validated['fields']);
        }

        // Build message payload
        $payload = [];
        
        if (!empty($validated['content'])) {
            $payload['content'] = $validated['content'];
        }

        if (!empty($embed)) {
            $payload['embeds'] = [$embed];
        }

        if (empty($payload)) {
            return redirect()->back()->with('error', 'Please add content or embed');
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => "Bot {$botToken}",
                'Content-Type' => 'application/json',
            ])->post("https://discord.com/api/channels/{$validated['channel_id']}/messages", $payload);

            if ($response->successful()) {
                return redirect()->back()->with('success', '✅ Message sent successfully!');
            } else {
                return redirect()->back()->with('error', 'Failed: ' . $response->body());
            }
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error: ' . $e->getMessage());
        }
    }
}
