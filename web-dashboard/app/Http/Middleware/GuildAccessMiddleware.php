<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class GuildAccessMiddleware
{
    /**
     * Verify user has access to the specified guild
     */
    public function handle(Request $request, Closure $next): Response
    {
        $guildId = $request->route('guildId');
        
        if (!$guildId) {
            return redirect()->route('select-server');
        }

        // Get user's guilds from session
        $guilds = session('discord_guilds', []);
        
        // Check if user has access to this guild
        $hasAccess = false;
        $selectedGuild = null;
        
        foreach ($guilds as $guild) {
            if ($guild['id'] === $guildId) {
                $hasAccess = true;
                $selectedGuild = $guild;
                break;
            }
        }
        
        if (!$hasAccess) {
            return redirect()->route('select-server')
                ->with('error', 'Kamu tidak memiliki akses ke server ini.');
        }
        
        // Store the selected guild in session for easy access
        session(['selected_guild_id' => $guildId]);
        session(['selected_guild' => $selectedGuild]);
        
        return $next($request);
    }
}
