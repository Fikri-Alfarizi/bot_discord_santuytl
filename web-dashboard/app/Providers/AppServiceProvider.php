<?php

namespace App\Providers;

use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use SocialiteProviders\Manager\SocialiteWasCalled;
use SocialiteProviders\Discord\DiscordExtendSocialite;
use GuzzleHttp\Client;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Configure Guzzle HTTP client with SSL certificate for Discord OAuth
        $this->app->singleton(Client::class, function () {
            $cacertPath = base_path('cacert.pem');

            $options = [];
            if (file_exists($cacertPath)) {
                $options['verify'] = $cacertPath;
            }

            return new Client($options);
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Register Discord Socialite driver
        Event::listen(SocialiteWasCalled::class, DiscordExtendSocialite::class . '@handle');
    }
}
