<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function boot(): void
    {
        \Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::except([
            'api/*'
        ]);
    }
}
