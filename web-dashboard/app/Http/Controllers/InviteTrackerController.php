<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class InviteTrackerController extends Controller
{
    public function index()
    {
        $guildId = session('selected_guild_id');
        
        // Calculate Invite Counts
        // Regular: Total valid invites
        // Left: Users who left
        // Fake: Users who are fake (short account age, etc - logic to be refined in bot)
        // Bonus: Manually added invites (Optional future feature)
        
        $inviters = DB::table('invites')
            ->select('inviter_id', 
                DB::raw('COUNT(*) as total'),
                DB::raw('SUM(CASE WHEN is_valid = 1 AND is_fake = 0 AND is_left = 0 THEN 1 ELSE 0 END) as regular'),
                DB::raw('SUM(CASE WHEN is_fake = 1 THEN 1 ELSE 0 END) as fake'),
                DB::raw('SUM(CASE WHEN is_left = 1 THEN 1 ELSE 0 END) as left_count')
            )
            ->where('guild_id', $guildId)
            ->groupBy('inviter_id')
            ->orderByDesc('regular')
            ->limit(50)
            ->get();
            
        // Fetch user data for display (username, avatar)
        // We try to get from local 'users' table first, defaulting to Discord API if missing
        $userIds = $inviters->pluck('inviter_id')->toArray();
        $localUsers = DB::table('users')->whereIn('id', $userIds)->get()->keyBy('id');
        
        $enrichedInviters = $inviters->map(function ($inviter) use ($localUsers) {
            $user = $localUsers->get($inviter->inviter_id);
            $inviter->username = $user->username ?? 'Unknown User';
            $inviter->avatar = $user->avatar ?? null;
            return $inviter;
        });

        return view('invite-tracker.index', [
            'inviters' => $enrichedInviters
        ]);
    }
}
