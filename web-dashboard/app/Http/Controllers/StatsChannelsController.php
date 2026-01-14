<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class StatsChannelsController extends Controller
{
    public function index()
    {
        $guildId = session('selected_guild_id');
        
        $statsChannels = DB::table('stats_channels')->where('guild_id', $guildId)->get();
        
        return view('stats-channels.index', compact('statsChannels'));
    }

    public function store(Request $request)
    {
        $guildId = session('selected_guild_id');
        
        $request->validate([
            'type' => 'required|string',
            'format' => 'required|string',
        ]);

        // Send to Bot to Create Channel
        $payload = [
            'type' => $request->type,
            'format' => $request->format,
            'data' => $request->data ?? null, // e.g. role ID
        ];

        DB::table('bot_outbox')->insert([
            'guild_id' => $guildId,
            'channel_id' => 'create_stats', // Special marker
            'type' => 'stats_create',
            'payload' => json_encode($payload),
            'processed' => false,
            'created_at' => now(),
        ]);

        return redirect()->back()->with('success', 'Request sent to bot! Channel will appear shortly.');
    }

    public function destroy($id)
    {
        $channel = DB::table('stats_channels')->where('id', $id)->first();
        if ($channel) {
            // Send request to delete discord channel
             DB::table('bot_outbox')->insert([
                'guild_id' => $channel->guild_id,
                'channel_id' => $channel->channel_id,
                'type' => 'stats_delete',
                'payload' => json_encode([]),
                'processed' => false,
                'created_at' => now(),
            ]);

            DB::table('stats_channels')->where('id', $id)->delete();
        }
        
        return redirect()->back()->with('success', 'Stats channel deleted.');
    }
}
