<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminGuildsController extends Controller
{
    /**
     * List all guilds with settings
     */
    public function index()
    {
        $guilds = DB::table('guild_settings')->get();

        $stats = [
            'total' => $guilds->count(),
            'with_welcome' => $guilds->whereNotNull('welcome_channel_id')->count(),
            'with_logs' => $guilds->whereNotNull('log_channel_id')->count(),
            'with_news' => $guilds->whereNotNull('news_channel_id')->count(),
        ];

        return view('admin.guilds', compact('guilds', 'stats'));
    }

    /**
     * View guild details
     */
    public function show($guildId)
    {
        $guild = DB::table('guild_settings')->where('guild_id', $guildId)->first();
        
        if (!$guild) {
            return redirect()->route('admin.guilds')->with('error', 'Guild not found');
        }

        return view('admin.guilds.show', compact('guild'));
    }

    /**
     * Delete guild settings
     */
    public function destroy($guildId)
    {
        DB::table('guild_settings')->where('guild_id', $guildId)->delete();

        return redirect()->route('admin.guilds')->with('success', 'Guild settings deleted!');
    }
}
