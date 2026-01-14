<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminEconomyController extends Controller
{
    /**
     * Economy overview and controls
     */
    public function index()
    {
        $stats = [
            'total_coins' => DB::table('users')->sum('coins') ?? 0,
            'total_xp' => DB::table('users')->sum('xp') ?? 0,
            'avg_coins' => round(DB::table('users')->avg('coins') ?? 0),
            'avg_level' => round(DB::table('users')->avg('level') ?? 0, 1),
            'richest' => DB::table('users')->orderByDesc('coins')->first(),
            'highest_level' => DB::table('users')->orderByDesc('level')->first(),
        ];

        // Top 10 richest users
        $richestUsers = DB::table('users')
            ->orderByDesc('coins')
            ->limit(10)
            ->get(['id', 'username', 'coins', 'level']);

        return view('admin.economy', compact('stats', 'richestUsers'));
    }

    /**
     * Give coins to user
     */
    public function giveCoins(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|string',
            'amount' => 'required|integer',
        ]);

        DB::table('users')
            ->where('id', $validated['user_id'])
            ->increment('coins', $validated['amount']);

        return redirect()->back()->with('success', "Gave {$validated['amount']} coins!");
    }

    /**
     * Mass give coins to all users
     */
    public function massGive(Request $request)
    {
        $validated = $request->validate([
            'amount' => 'required|integer|min:1|max:100000',
        ]);

        $affected = DB::table('users')->increment('coins', $validated['amount']);

        return redirect()->back()->with('success', "Gave {$validated['amount']} coins to {$affected} users!");
    }

    /**
     * Reset all economy data (dangerous!)
     */
    public function resetAll(Request $request)
    {
        if ($request->get('confirm') !== 'RESET') {
            return redirect()->back()->with('error', 'Confirmation failed');
        }

        DB::table('users')->update([
            'coins' => 0,
            'xp' => 0,
            'level' => 1,
            'seasonal_xp' => 0,
        ]);

        return redirect()->back()->with('success', 'All economy data reset!');
    }
}
