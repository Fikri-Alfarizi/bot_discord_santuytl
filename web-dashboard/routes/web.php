<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DiscordAuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ServerSelectController;
use App\Http\Controllers\EmbedController;
use App\Http\Controllers\PluginsController;
use App\Http\Controllers\WelcomeController;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\Admin\AdminEconomyController;
use App\Http\Controllers\Admin\AdminSeasonController;
use App\Http\Controllers\Admin\AdminModerationController;
use App\Http\Controllers\Admin\AdminAnalyticsController;
use App\Http\Controllers\Admin\AdminGuildsController;

// Public Routes
Route::get('/', function () {
    return view('welcome');
});

// Debug Route
Route::get('/debug-columns', function () {
    return response()->json(\Illuminate\Support\Facades\Schema::getColumnListing('guild_settings'));
});

// Discord OAuth Routes
Route::get('/auth/discord', [DiscordAuthController::class, 'redirect'])->name('auth.discord');
Route::get('/login', [DiscordAuthController::class, 'redirect'])->name('login'); // Alias for auth middleware
Route::get('/auth/discord/callback', [DiscordAuthController::class, 'callback']);
Route::post('/logout', [DiscordAuthController::class, 'logout'])->name('logout');

// Server Selection (must be logged in)
Route::middleware(['auth'])->group(function () {
    Route::get('/select-server', [ServerSelectController::class, 'index'])->name('select-server');
    Route::post('/select-server', [ServerSelectController::class, 'select'])->name('select-server.select');
    
    // Redirect old dashboard to server selection
    Route::get('/dashboard', function () {
        $guildId = session('selected_guild_id');
        if ($guildId) {
            return redirect()->route('dashboard', ['guildId' => $guildId]);
        }
        return redirect()->route('select-server');
    })->name('dashboard.redirect');
});

// Member Community Hub "Santuy World"
Route::middleware(['auth'])->prefix('world')->name('member.')->group(function () {
    Route::get('/', [App\Http\Controllers\Member\HubController::class, 'index'])->name('hub');
    Route::get('/market', [App\Http\Controllers\Member\HubController::class, 'market'])->name('market');
    Route::get('/leaderboard', [App\Http\Controllers\Member\HubController::class, 'leaderboard'])->name('leaderboard');
    Route::get('/profile', [App\Http\Controllers\Member\HubController::class, 'profile'])->name('profile');
    Route::get('/inventory', [App\Http\Controllers\Member\HubController::class, 'inventory'])->name('inventory');
    Route::post('/buy', [App\Http\Controllers\Member\HubController::class, 'buy'])->name('buy');
    Route::post('/daily', [App\Http\Controllers\Member\HubController::class, 'daily'])->name('daily');
});

// Guild-specific Dashboard Routes (requires guild access)
Route::middleware(['auth', 'guild'])->prefix('dashboard/{guildId}')->group(function () {
    // Dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/leaderboard', [DashboardController::class, 'leaderboard'])->name('dashboard.leaderboard');
    Route::get('/settings', [DashboardController::class, 'settings'])->name('dashboard.settings');
    Route::post('/settings/update', [DashboardController::class, 'updateSettings'])->name('dashboard.settings.update');
    
    // Plugins Page
    Route::get('/plugins', [PluginsController::class, 'index'])->name('plugins');
    
    // Embed Builder
    Route::get('/embed-builder', [EmbedController::class, 'index'])->name('embed.index');
    Route::post('/embed-builder/send', [EmbedController::class, 'send'])->name('embed.send');

    // Welcome & Goodbye
    Route::get('/welcome', [WelcomeController::class, 'index'])->name('welcome.index');
    Route::post('/welcome/update', [WelcomeController::class, 'update'])->name('welcome.update');
    Route::post('/welcome/test', [WelcomeController::class, 'test'])->name('welcome.test');

    // Reaction Roles
    Route::get('/reaction-roles', [App\Http\Controllers\ReactionRoleController::class, 'index'])->name('reaction-roles.index');
    Route::post('/reaction-roles/store', [App\Http\Controllers\ReactionRoleController::class, 'store'])->name('reaction-roles.store');
    Route::delete('/reaction-roles/{id}', [App\Http\Controllers\ReactionRoleController::class, 'destroy'])->name('reaction-roles.destroy');

    // Moderator
    Route::get('/moderator', [App\Http\Controllers\ModeratorController::class, 'index'])->name('moderator.index');
    Route::post('/moderator/update', [App\Http\Controllers\ModeratorController::class, 'updateRules'])->name('moderator.update');
    Route::delete('/moderator/{id}', [App\Http\Controllers\ModeratorController::class, 'destroyWarn'])->name('moderator.destroy');

    // Level Rewards
    Route::get('/levels', [App\Http\Controllers\LevelsController::class, 'index'])->name('levels.index');
    Route::post('/levels/store', [App\Http\Controllers\LevelsController::class, 'store'])->name('levels.store');
    Route::delete('/levels/{id}', [App\Http\Controllers\LevelsController::class, 'destroy'])->name('levels.destroy');

    // Welcome Channel
    Route::get('/welcome-channel', [App\Http\Controllers\WelcomeChannelController::class, 'index'])->name('welcome-channel.index');
    Route::post('/welcome-channel/store', [App\Http\Controllers\WelcomeChannelController::class, 'store'])->name('welcome-channel.store');
    Route::post('/welcome-channel/deploy', [App\Http\Controllers\WelcomeChannelController::class, 'deploy'])->name('welcome-channel.deploy');

    // Custom Commands
    Route::get('/custom-commands', [App\Http\Controllers\CustomCommandsController::class, 'index'])->name('custom-commands.index');
    Route::post('/custom-commands/store', [App\Http\Controllers\CustomCommandsController::class, 'store'])->name('custom-commands.store');
    Route::delete('/custom-commands/{id}', [App\Http\Controllers\CustomCommandsController::class, 'destroy'])->name('custom-commands.destroy');

    // Invite Tracker
    Route::get('/invite-tracker', [App\Http\Controllers\InviteTrackerController::class, 'index'])->name('invite-tracker.index');

    // Ticketing
    Route::get('/ticketing', [App\Http\Controllers\TicketingController::class, 'index'])->name('ticketing.index');
    Route::post('/ticketing/store', [App\Http\Controllers\TicketingController::class, 'store'])->name('ticketing.store');
    Route::post('/ticketing/deploy', [App\Http\Controllers\TicketingController::class, 'deploy'])->name('ticketing.deploy');

    // Emojis
    Route::get('/emojis', [App\Http\Controllers\EmojisController::class, 'index'])->name('emojis.index');

    // Polls
    Route::get('/polls', [App\Http\Controllers\PollsController::class, 'index'])->name('polls.index');
    Route::post('/polls/store', [App\Http\Controllers\PollsController::class, 'store'])->name('polls.store');

    // Stats Channels (PRO)
    Route::middleware([\App\Http\Middleware\CheckProStatus::class])->group(function () {
        Route::get('/stats-channels', [App\Http\Controllers\StatsChannelsController::class, 'index'])->name('stats-channels.index');
        Route::post('/stats-channels/store', [App\Http\Controllers\StatsChannelsController::class, 'store'])->name('stats-channels.store');
        Route::delete('/stats-channels/{id}', [App\Http\Controllers\StatsChannelsController::class, 'destroy'])->name('stats-channels.destroy');
    });

    // Reminders
    Route::get('/reminders', [App\Http\Controllers\RemindersController::class, 'index'])->name('reminders.index');
    Route::post('/reminders/store', [App\Http\Controllers\RemindersController::class, 'store'])->name('reminders.store');
    Route::delete('/reminders/{id}', [App\Http\Controllers\RemindersController::class, 'destroy'])->name('reminders.destroy');

    // Temp Channels
    Route::get('/temp-channels', [App\Http\Controllers\TempChannelsController::class, 'index'])->name('temp-channels.index');
    Route::post('/temp-channels/store', [App\Http\Controllers\TempChannelsController::class, 'store'])->name('temp-channels.store');

    // Social Alerts (Refactored)
    // Twitch
    Route::get('/social/twitch', [App\Http\Controllers\TwitchAlertsController::class, 'index'])->name('twitch.index');
    Route::post('/social/twitch/store', [App\Http\Controllers\TwitchAlertsController::class, 'store'])->name('twitch.store');
    Route::delete('/social/twitch/{id}', [App\Http\Controllers\TwitchAlertsController::class, 'destroy'])->name('twitch.destroy');

    // RSS
    Route::get('/social/rss', [App\Http\Controllers\RssAlertsController::class, 'index'])->name('rss.index');
    Route::post('/social/rss/store', [App\Http\Controllers\RssAlertsController::class, 'store'])->name('rss.store');
    Route::delete('/social/rss/{id}', [App\Http\Controllers\RssAlertsController::class, 'destroy'])->name('rss.destroy');

    // Kick
    Route::get('/social/kick', [App\Http\Controllers\KickAlertsController::class, 'index'])->name('kick.index');
    Route::post('/social/kick/store', [App\Http\Controllers\KickAlertsController::class, 'store'])->name('kick.store');
    Route::delete('/social/kick/{id}', [App\Http\Controllers\KickAlertsController::class, 'destroy'])->name('kick.destroy');

    // YouTube
    Route::get('/social/youtube', [App\Http\Controllers\YoutubeAlertsController::class, 'index'])->name('youtube.index');
    Route::post('/social/youtube/store', [App\Http\Controllers\YoutubeAlertsController::class, 'store'])->name('youtube.store');
    Route::delete('/social/youtube/{id}', [App\Http\Controllers\YoutubeAlertsController::class, 'destroy'])->name('youtube.destroy');

    // TikTok
    Route::get('/social/tiktok', [App\Http\Controllers\TiktokAlertsController::class, 'index'])->name('tiktok.index');
    Route::post('/social/tiktok/store', [App\Http\Controllers\TiktokAlertsController::class, 'store'])->name('tiktok.store');
    Route::delete('/social/tiktok/{id}', [App\Http\Controllers\TiktokAlertsController::class, 'destroy'])->name('tiktok.destroy');

    // X (Twitter)
    Route::get('/social/x', [App\Http\Controllers\XAlertsController::class, 'index'])->name('x.index');
    Route::post('/social/x/store', [App\Http\Controllers\XAlertsController::class, 'store'])->name('x.store');
    Route::delete('/social/x/{id}', [App\Http\Controllers\XAlertsController::class, 'destroy'])->name('x.destroy');

    // Reddit
    Route::get('/social/reddit', [App\Http\Controllers\RedditAlertsController::class, 'index'])->name('reddit.index');
    Route::post('/social/reddit/store', [App\Http\Controllers\RedditAlertsController::class, 'store'])->name('reddit.store');
    Route::delete('/social/reddit/{id}', [App\Http\Controllers\RedditAlertsController::class, 'destroy'])->name('reddit.destroy');

    // Instagram
    Route::get('/social/instagram', [App\Http\Controllers\InstagramAlertsController::class, 'index'])->name('instagram.index');
    Route::post('/social/instagram/store', [App\Http\Controllers\InstagramAlertsController::class, 'store'])->name('instagram.store');
    Route::delete('/social/instagram/{id}', [App\Http\Controllers\InstagramAlertsController::class, 'destroy'])->name('instagram.destroy');

    // Automations
    Route::get('/automations', [App\Http\Controllers\AutomationsController::class, 'index'])->name('automations.index');
    Route::post('/automations/store', [App\Http\Controllers\AutomationsController::class, 'store'])->name('automations.store');
    Route::post('/automations/{id}/toggle', [App\Http\Controllers\AutomationsController::class, 'toggle'])->name('automations.toggle');
    Route::delete('/automations/{id}', [App\Http\Controllers\AutomationsController::class, 'destroy'])->name('automations.destroy');

    // Giveaways
    Route::get('/giveaways', [App\Http\Controllers\GiveawaysController::class, 'index'])->name('giveaways.index');
    Route::post('/giveaways/store', [App\Http\Controllers\GiveawaysController::class, 'store'])->name('giveaways.store');
    Route::delete('/giveaways/{id}', [App\Http\Controllers\GiveawaysController::class, 'destroy'])->name('giveaways.destroy');

    // Birthdays
    Route::get('/birthdays', [App\Http\Controllers\BirthdaysController::class, 'index'])->name('birthdays.index');
    Route::post('/birthdays/update', [App\Http\Controllers\BirthdaysController::class, 'update'])->name('birthdays.update');
    Route::delete('/birthdays/{id}', [App\Http\Controllers\BirthdaysController::class, 'destroy'])->name('birthdays.destroy');

    // SHOP & PREMIUM
    Route::get('/shop', [App\Http\Controllers\ShopController::class, 'index'])->name('shop.index');
    Route::post('/shop/buy-coins', [App\Http\Controllers\ShopController::class, 'buyCoins'])->name('shop.buyCoins');
    Route::post('/shop/buy-pro', [App\Http\Controllers\ShopController::class, 'buyPro'])->name('shop.buyPro');

});

// Admin Routes (Super Admin Only)
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    // Users Management
    Route::get('/users', [AdminUserController::class, 'index'])->name('users');
    Route::get('/users/{id}/edit', [AdminUserController::class, 'edit'])->name('users.edit');
    Route::put('/users/{id}', [AdminUserController::class, 'update'])->name('users.update');
    Route::post('/users/{id}/reset', [AdminUserController::class, 'reset'])->name('users.reset');
    Route::delete('/users/{id}', [AdminUserController::class, 'destroy'])->name('users.destroy');

    // Economy Controls
    Route::get('/economy', [AdminEconomyController::class, 'index'])->name('economy');
    Route::post('/economy/give', [AdminEconomyController::class, 'giveCoins'])->name('economy.give');
    Route::post('/economy/mass-give', [AdminEconomyController::class, 'massGive'])->name('economy.massGive');
    Route::post('/economy/reset', [AdminEconomyController::class, 'resetAll'])->name('economy.reset');

    // Season Manager
    Route::get('/seasons', [AdminSeasonController::class, 'index'])->name('seasons');
    Route::post('/seasons/start', [AdminSeasonController::class, 'start'])->name('seasons.start');
    Route::post('/seasons/end', [AdminSeasonController::class, 'end'])->name('seasons.end');

    // Moderation
    Route::get('/moderation', [AdminModerationController::class, 'index'])->name('moderation');
    Route::post('/moderation/trust', [AdminModerationController::class, 'updateTrust'])->name('moderation.trust');
    Route::post('/moderation/trust/{userId}/reset', [AdminModerationController::class, 'resetTrust'])->name('moderation.trust.reset');

    // Analytics
    Route::get('/analytics', [AdminAnalyticsController::class, 'index'])->name('analytics');

    // Guilds
    Route::get('/guilds', [AdminGuildsController::class, 'index'])->name('guilds');
    Route::get('/guilds/{guildId}', [AdminGuildsController::class, 'show'])->name('guilds.show');
    Route::delete('/guilds/{guildId}', [AdminGuildsController::class, 'destroy'])->name('guilds.destroy');
});
