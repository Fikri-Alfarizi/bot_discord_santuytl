<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;

class ShopController extends Controller
{
    public function index()
    {
        $guildId = session('selected_guild_id');
        $user = DB::table('users')->where('id', Auth::user()->discord_id)->first();
        
        // Get Guild Settings for Pro status
        $settings = null;
        if ($guildId) {
            $settings = DB::table('guild_settings')->where('guild_id', $guildId)->first();
            if (!$settings) {
                // Auto create settings if missing
                DB::table('guild_settings')->insert(['guild_id' => $guildId, 'created_at' => now(), 'updated_at' => now()]);
                $settings = DB::table('guild_settings')->where('guild_id', $guildId)->first();
            }
        }

        // Coin Packages
        $coinPackages = [
            ['id' => 'coins_50k', 'name' => 'Starter Pack', 'coins' => 50000, 'price' => '$5 (Simulated)', 'fa_icon' => 'fa-coins'],
            ['id' => 'coins_250k', 'name' => 'Rich Pack', 'coins' => 250000, 'price' => '$20 (Simulated)', 'fa_icon' => 'fa-sack-dollar'],
            ['id' => 'coins_1m', 'name' => 'Millionaire Pack', 'coins' => 1000000, 'price' => '$50 (Simulated)', 'fa_icon' => 'fa-crown'],
        ];

        // Pro Subscriptions (Buy with Coins)
        // Prices: 1M Coins = 30 Days. 
        // Daily = 50k. Yearly = 10M. Lifetime = 50M.
        $proPackages = [
            ['id' => 'pro_daily', 'name' => 'Pro (1 Day)', 'duration_days' => 1, 'price_coins' => 50000],
            ['id' => 'pro_monthly', 'name' => 'Pro (30 Days)', 'duration_days' => 30, 'price_coins' => 1000000],
            ['id' => 'pro_yearly', 'name' => 'Pro (365 Days)', 'duration_days' => 365, 'price_coins' => 10000000],
            ['id' => 'pro_lifetime', 'name' => 'Pro (Lifetime)', 'duration_days' => 36500, 'price_coins' => 50000000],
        ];

        return view('shop.index', [
            'coinPackages' => $coinPackages,
            'proPackages' => $proPackages,
            'userCoins' => $user->coins ?? 0,
            'proExpiresAt' => $settings->pro_expires_at ?? null,
            'settings' => $settings,
            'guildId' => $guildId
        ]);
    }

    public function buyCoins(Request $request)
    {
        $request->validate(['package_id' => 'required']);
        $packageId = $request->package_id;
        
        $coinsToAdd = 0;
        if ($packageId === 'coins_50k') $coinsToAdd = 50000;
        else if ($packageId === 'coins_250k') $coinsToAdd = 250000;
        else if ($packageId === 'coins_1m') $coinsToAdd = 1000000;

        if ($coinsToAdd > 0) {
            DB::table('users')->where('id', Auth::user()->discord_id)->increment('coins', $coinsToAdd);
            
            // Log Transaction
            DB::table('economy_transactions')->insert([
                'user_id' => Auth::user()->discord_id,
                'type' => 'shop_buy_coins',
                'amount' => $coinsToAdd,
                'description' => 'Purchased ' . $packageId,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            return redirect()->back()->with('success', 'Successfully purchased ' . number_format($coinsToAdd) . ' coins!');
        }

        return redirect()->back()->with('error', 'Invalid package.');
    }

    public function buyPro(Request $request)
    {
        $request->validate(['package_id' => 'required']);
        $guildId = session('selected_guild_id');
        if (!$guildId) return redirect()->back()->with('error', 'No server selected.');

        $packageId = $request->package_id;
        $price = 0;
        $days = 0;

        if ($packageId === 'pro_daily') { $price = 50000; $days = 1; }
        elseif ($packageId === 'pro_monthly') { $price = 1000000; $days = 30; }
        elseif ($packageId === 'pro_yearly') { $price = 10000000; $days = 365; }
        elseif ($packageId === 'pro_lifetime') { $price = 50000000; $days = 36500; }

        if ($price === 0) return redirect()->back()->with('error', 'Invalid package.');

        $user = DB::table('users')->where('id', Auth::user()->discord_id)->first();
        if (($user->coins ?? 0) < $price) {
            return redirect()->back()->with('error', 'Insufficient coins. You need ' . number_format($price) . ' coins.');
        }

        // Deduct Coins
        DB::table('users')->where('id', Auth::user()->discord_id)->decrement('coins', $price);

        // Update Guild Pro Status
        $settings = DB::table('guild_settings')->where('guild_id', $guildId)->first();
        $currentExpiry = $settings->pro_expires_at ? Carbon::parse($settings->pro_expires_at) : Carbon::now();
        
        // If expired, start from now. If active, add to current expiry.
        if ($currentExpiry->isPast()) $currentExpiry = Carbon::now();
        $newExpiry = $currentExpiry->addDays($days);

        DB::table('guild_settings')->updateOrInsert(
            ['guild_id' => $guildId],
            ['pro_expires_at' => $newExpiry, 'updated_at' => now()]
        );

        // Log Transaction
        DB::table('economy_transactions')->insert([
            'user_id' => Auth::user()->discord_id,
            'type' => 'shop_buy_pro',
            'amount' => -$price,
            'description' => 'Purchased Pro for Guild ' . $guildId . ' (' . $days . ' days)',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->back()->with('success', 'Successfully upgraded server to Pro! Expires: ' . $newExpiry->format('d M Y'));
    }
}
