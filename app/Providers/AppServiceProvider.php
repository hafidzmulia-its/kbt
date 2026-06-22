<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
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
        if ($this->app->runningUnitTests()) {
            $testStoragePath = rtrim(sys_get_temp_dir(), DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR.'kbt-storage';
            $compiledPath = $testStoragePath.DIRECTORY_SEPARATOR.'framework'.DIRECTORY_SEPARATOR.'views';

            $this->app->useStoragePath($testStoragePath);

            if (! is_dir($compiledPath)) {
                mkdir($compiledPath, 0777, true);
            }

            config()->set('view.compiled', $compiledPath);
            config()->set('logging.default', 'errorlog');
        }

        RateLimiter::for('public-invitation', function (Request $request) {
            return Limit::perMinute(60)->by($request->ip());
        });

        RateLimiter::for('public-interaction', function (Request $request) {
            return Limit::perMinute(20)->by($request->ip());
        });
    }
}
