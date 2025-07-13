<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;
use App\Helpers\AssetHelper;

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
        // Register Blade directives for versioned assets
        Blade::directive('versioned', function ($expression) {
            return "<?php echo App\Helpers\AssetHelper::versioned($expression); ?>";
        });

        Blade::directive('versionedJs', function ($expression) {
            return "<?php echo App\Helpers\AssetHelper::js($expression); ?>";
        });

        Blade::directive('versionedCss', function ($expression) {
            return "<?php echo App\Helpers\AssetHelper::css($expression); ?>";
        });
    }
}
