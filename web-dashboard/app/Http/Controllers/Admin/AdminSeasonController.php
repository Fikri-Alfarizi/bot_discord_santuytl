<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminSeasonController extends Controller
{
    /**
     * Season management page
     */
    public function index()
    {
        $currentSeason = DB::table('seasons')
            ->where('is_active', 1)
            ->first();

        $allSeasons = DB::table('seasons')
            ->orderByDesc('season_number')
            ->get();

        $topPlayers = [];
        if ($currentSeason) {
            $topPlayers = DB::table('users')
                ->orderByDesc('seasonal_xp')
                ->limit(10)
                ->get(['id', 'username', 'seasonal_xp', 'level']);
        }

        return view('admin.seasons', compact('currentSeason', 'allSeasons', 'topPlayers'));
    }

    /**
     * Start new season
     */
    public function start(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        // End current season
        DB::table('seasons')->where('is_active', 1)->update([
            'is_active' => 0,
            'end_date' => time(),
        ]);

        // Reset seasonal XP
        DB::table('users')->update(['seasonal_xp' => 0]);

        // Get next season number
        $lastSeason = DB::table('seasons')->max('season_number') ?? 0;

        // Create new season
        DB::table('seasons')->insert([
            'season_number' => $lastSeason + 1,
            'name' => $validated['name'],
            'start_date' => time(),
            'is_active' => 1,
        ]);

        return redirect()->back()->with('success', 'New season started!');
    }

    /**
     * End current season
     */
    public function end()
    {
        DB::table('seasons')->where('is_active', 1)->update([
            'is_active' => 0,
            'end_date' => time(),
        ]);

        return redirect()->back()->with('success', 'Season ended!');
    }
}
