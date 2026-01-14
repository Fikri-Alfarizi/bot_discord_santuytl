<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;
use Carbon\Carbon;

class CheckProStatus
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $guildId = $request->route('guildId') ?? session('selected_guild_id');

        if (!$guildId) {
            return redirect()->route('select-server');
        }

        $settings = DB::table('guild_settings')->where('guild_id', $guildId)->first();

        // Check if Pro exists and is not expired
        if (!$settings || !$settings->pro_expires_at || Carbon::parse($settings->pro_expires_at)->isPast()) {
            // Check if Super Admin bypass? (Optional, let's stick to strict Check for now)
            // if (in_array(auth()->user()->discord_id, ['admin_id'])) return $next($request);

            return redirect()->route('shop.index', ['guildId' => $guildId])
                ->with('error', '🔒 This feature requires a Pro subscription. Please upgrade your server.');
        }

        return $next($request);
    }
}
