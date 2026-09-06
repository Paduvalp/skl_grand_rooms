<?php

namespace App\Providers;

use App\Models\Setting;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /** Cached for the current request so we hit the database only once. */
    private ?array $siteSettings = null;

    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Pagination links must match the Bootstrap 5 look of the site.
        Paginator::useBootstrapFive();

        // Make site settings available inside every Blade view as $settings.
        View::composer('*', function ($view) {
            $view->with('settings', $this->siteSettings());
        });
    }

    private function siteSettings(): array
    {
        if ($this->siteSettings !== null) {
            return $this->siteSettings;
        }

        try {
            $this->siteSettings = Schema::hasTable('settings')
                ? Setting::all()->pluck('value', 'key')->toArray()
                : [];
        } catch (\Throwable $e) {
            // Database not ready yet (before migrate) - fall back to defaults.
            $this->siteSettings = [];
        }

        return $this->siteSettings;
    }
}
