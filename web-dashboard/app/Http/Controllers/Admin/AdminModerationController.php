<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminModerationController extends Controller
{
    /**
     * Moderation overview
     */
    public function index()
    {
        // Get users with low trust scores
        $lowTrust = DB::table('trust_score')
            ->where('score', '<', 50)
            ->orderBy('score')
            ->limit(20)
            ->get();

        // Get top reputation users
        $topRep = DB::table('reputation')
            ->orderByDesc('rep_points')
            ->limit(10)
            ->get();

        // Stats
        $stats = [
            'total_trust_entries' => DB::table('trust_score')->count(),
            'avg_trust' => round(DB::table('trust_score')->avg('score') ?? 100),
            'total_rep' => DB::table('reputation')->sum('rep_points') ?? 0,
        ];

        return view('admin.moderation', compact('lowTrust', 'topRep', 'stats'));
    }

    /**
     * Update trust score
     */
    public function updateTrust(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|string',
            'score' => 'required|integer|min:0|max:100',
            'reason' => 'nullable|string',
        ]);

        DB::table('trust_score')->updateOrInsert(
            ['user_id' => $validated['user_id']],
            [
                'score' => $validated['score'],
                'reason' => $validated['reason'] ?? null,
            ]
        );

        return redirect()->back()->with('success', 'Trust score updated!');
    }

    /**
     * Reset user trust to 100
     */
    public function resetTrust($userId)
    {
        DB::table('trust_score')->updateOrInsert(
            ['user_id' => $userId],
            ['score' => 100, 'reason' => 'Reset by admin']
        );

        return redirect()->back()->with('success', 'Trust score reset to 100!');
    }
}
