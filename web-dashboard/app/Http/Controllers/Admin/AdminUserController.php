<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminUserController extends Controller
{
    /**
     * List all users with search/filter
     */
    public function index(Request $request)
    {
        $search = $request->get('search', '');
        $sortBy = $request->get('sort', 'level');
        $order = $request->get('order', 'desc');

        $query = DB::table('users');

        if ($search) {
            $query->where('username', 'like', "%{$search}%")
                  ->orWhere('id', 'like', "%{$search}%");
        }

        $users = $query->orderBy($sortBy, $order)
                       ->paginate(20);

        $stats = [
            'total' => DB::table('users')->count(),
            'total_coins' => DB::table('users')->sum('coins'),
            'total_xp' => DB::table('users')->sum('xp'),
            'avg_level' => round(DB::table('users')->avg('level') ?? 0, 1),
        ];

        return view('admin.users', compact('users', 'stats', 'search', 'sortBy', 'order'));
    }

    /**
     * Edit user form
     */
    public function edit($id)
    {
        $user = DB::table('users')->where('id', $id)->first();
        
        if (!$user) {
            return redirect()->route('admin.users')->with('error', 'User not found');
        }

        $inventory = DB::table('inventory')->where('user_id', $id)->get();

        return view('admin.users.edit', compact('user', 'inventory'));
    }

    /**
     * Update user data
     */
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'username' => 'nullable|string',
            'xp' => 'required|integer|min:0',
            'level' => 'required|integer|min:1',
            'coins' => 'required|integer|min:0',
            'seasonal_xp' => 'required|integer|min:0',
        ]);

        DB::table('users')->where('id', $id)->update($validated);

        return redirect()->route('admin.users.edit', $id)->with('success', 'User updated!');
    }

    /**
     * Reset user data
     */
    public function reset($id)
    {
        DB::table('users')->where('id', $id)->update([
            'xp' => 0,
            'level' => 1,
            'coins' => 0,
            'seasonal_xp' => 0,
            'last_daily' => 0,
            'last_weekly' => 0,
        ]);

        return redirect()->back()->with('success', 'User data reset!');
    }

    /**
     * Delete user
     */
    public function destroy($id)
    {
        DB::table('users')->where('id', $id)->delete();
        DB::table('inventory')->where('user_id', $id)->delete();
        DB::table('reputation')->where('user_id', $id)->delete();
        DB::table('trust_score')->where('user_id', $id)->delete();

        return redirect()->route('admin.users')->with('success', 'User deleted!');
    }
}
