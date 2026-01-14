<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class DiscordAuthController extends Controller
{
    /**
     * Redirect to Discord OAuth
     */
    public function redirect()
    {
        return Socialite::driver('discord')
            ->scopes(['identify', 'email', 'guilds'])
            ->redirect();
    }

    /**
     * Handle callback from Discord
     */
    public function callback()
    {
        try {
            $discordUser = Socialite::driver('discord')->user();

            $user = User::updateOrCreate(
                ['discord_id' => $discordUser->getId()],
                [
                    'name' => $discordUser->getName() ?? $discordUser->getNickname(),
                    'email' => $discordUser->getEmail(),
                    'avatar' => $discordUser->getAvatar(),
                ]
            );

            // Store guilds in session for server selection
            $guilds = [];
            try {
                $context = stream_context_create([
                    'http' => [
                        'header' => "Authorization: Bearer " . $discordUser->token . "\r\n",
                        'ignore_errors' => true,
                    ]
                ]);
                $guildsResponse = file_get_contents('https://discord.com/api/users/@me/guilds', false, $context);

                if ($guildsResponse === false) {
                    \Log::warning('Discord guilds fetch failed: file_get_contents returned false');
                } else {
                    $allGuilds = json_decode($guildsResponse, true);

                    if (is_array($allGuilds)) {
                        // Filter only guilds where user has ADMINISTRATOR or MANAGE_GUILD permission
                        foreach ($allGuilds as $guild) {
                            $permissions = intval($guild['permissions'] ?? 0);
                            // ADMINISTRATOR = 0x8, MANAGE_GUILD = 0x20
                            if (($permissions & 0x8) || ($permissions & 0x20)) {
                                $guilds[] = $guild;
                            }
                        }

                        // If no admin guilds found, include ALL guilds so user can at least select one
                        if (empty($guilds) && !empty($allGuilds)) {
                            \Log::info('No admin guilds found, including all user guilds');
                            $guilds = $allGuilds;
                        }
                    } else {
                        \Log::warning('Discord guilds response not an array', ['response' => $guildsResponse]);
                    }
                }
            } catch (\Exception $e) {
                \Log::error('Failed to fetch Discord guilds: ' . $e->getMessage());
            }

            // Auto-select first guild if available
            $selectedGuildId = count($guilds) > 0 ? $guilds[0]['id'] : null;
            $selectedGuild = count($guilds) > 0 ? $guilds[0] : null;

            session([
                'discord_guilds' => $guilds,
                'discord_token' => $discordUser->token,
                'selected_guild_id' => $selectedGuildId,
                'selected_guild' => $selectedGuild,
            ]);

            Auth::login($user);

            // Handle redirect based on guild count
            if (empty($guilds)) {
                // No guilds found - redirect to home with error
                return redirect('/')->with('error', 'Tidak ditemukan server Discord. Pastikan Anda adalah anggota setidaknya satu server.');
            } elseif (count($guilds) === 1) {
                // Single guild - redirect directly to dashboard
                return redirect()->route('dashboard', ['guildId' => $selectedGuildId]);
            } else {
                // Multiple guilds - let user choose
                return redirect()->route('select-server');
            }
        } catch (\Exception $e) {
            \Log::error('Discord login failed: ' . $e->getMessage());
            return redirect('/')->with('error', 'Login gagal: ' . $e->getMessage());
        }
    }

    /**
     * Logout
     */
    public function logout()
    {
        Auth::logout();
        session()->forget(['discord_guilds', 'discord_token']);
        return redirect('/');
    }
}
