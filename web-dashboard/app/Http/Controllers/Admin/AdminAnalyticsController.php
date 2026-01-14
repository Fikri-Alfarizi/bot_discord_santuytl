<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminAnalyticsController extends Controller
{
    /**
     * Analytics dashboard
     */
    public function index()
    {
        // User stats
        $userStats = [
            'total' => DB::table('users')->count(),
            'with_coins' => DB::table('users')->where('coins', '>', 0)->count(),
            'high_level' => DB::table('users')->where('level', '>=', 10)->count(),
        ];

        // Level distribution
        $levelDistribution = DB::table('users')
            ->selectRaw('level, COUNT(*) as count')
            ->groupBy('level')
            ->orderBy('level')
            ->get();

        // Top 10 by various metrics
        $topByXP = DB::table('users')->orderByDesc('xp')->limit(5)->get(['username', 'xp']);
        $topByCoins = DB::table('users')->orderByDesc('coins')->limit(5)->get(['username', 'coins']);
        $topByLevel = DB::table('users')->orderByDesc('level')->limit(5)->get(['username', 'level']);

        // Guild stats
        $guildStats = [
            'total' => DB::table('guild_settings')->count(),
            'with_welcome' => DB::table('guild_settings')->whereNotNull('welcome_channel_id')->count(),
            'with_news' => DB::table('guild_settings')->whereNotNull('news_channel_id')->count(),
        ];

        return view('admin.analytics', compact('userStats', 'levelDistribution', 'topByXP', 'topByCoins', 'topByLevel', 'guildStats'));
    }
}
