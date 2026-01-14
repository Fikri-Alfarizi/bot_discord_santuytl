<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use GuzzleHttp\Client;

class SocialiteServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Configure Guzzle HTTP client with correct SSL certificate
        $cacertPath = base_path('cacert.pem');

        if (file_exists($cacertPath)) {
            // Set for Guzzle globally
            $this->app->bind(Client::class, function () use ($cacertPath) {
                return new Client([
                    'verify' => $cacertPath,
                ]);
            });
        }
    }
}
