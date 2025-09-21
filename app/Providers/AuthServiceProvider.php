<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        \Illuminate\Support\Facades\Gate::policy(
            \App\Models\ServerCredential::class,
            \App\Policies\ServerCredentialPolicy::class
        );
    }
}
