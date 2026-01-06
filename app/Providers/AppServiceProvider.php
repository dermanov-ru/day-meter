<?php

namespace App\Providers;

use App\Models\CulturalActivity;
use App\Models\Disease;
use App\Policies\CulturalActivityPolicy;
use App\Policies\DiseasePolicy;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Route;

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
        Gate::policy(Disease::class, DiseasePolicy::class);
        Gate::policy(CulturalActivity::class, CulturalActivityPolicy::class);

        // Route model binding
        Route::model('culturalActivity', CulturalActivity::class);

        // Force HTTPS in production
        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }
    }
}
