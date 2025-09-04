<?php

namespace OtpShield;

use Illuminate\Support\ServiceProvider;
use OtpShield\Services\OtpService;

class OtpShieldServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton('otpshield', fn() => new OtpService());
    }

    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../config/otp-shield.php' => config_path('otp-shield.php'),
        ], 'config');

        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');

        if ($this->app->runningInConsole()) {
            $this->commands([
                \OtpShield\Commands\EnableOtp::class,
                \OtpShield\Commands\DisableOtp::class,
            ]);
        }
    }
}
