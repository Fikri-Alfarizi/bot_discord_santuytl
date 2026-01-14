<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Super Admin Discord IDs
     */
    protected array $adminIds = [
        '1155782329332146238', // Fikri
    ];

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user || !in_array($user->discord_id, $this->adminIds)) {
            abort(403, 'Access denied. Admin only.');
        }

        return $next($request);
    }
}
