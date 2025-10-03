<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="antialiased">
    <head>
        @include('partials.head')
    </head>
    <body>
        <flux:header container class="[&>div]:max-w-2xl! min-h-18">
            <flux:brand href="route('dashboard')" :name="config('app.name')" class="[&>div]:first:hidden" />

            <flux:spacer />

            <div class="flex gap-2">
                @guest
                    <flux:button :href="route('login')" variant="ghost" size="sm">{{ __('Sign in') }}</flux:button>
                    <flux:button :href="route('register')" size="sm">{{ __('Get Started') }}</flux:button>
                @else
                    <flux:button :href="route('dashboard')" size="sm">{{ __('Dashboard') }}</flux:button>
                @endguest
            </div>
        </flux:header>

        <flux:main class="[:where(&)]:max-w-2xl!" container>
            <flux:heading level="1" size="xl" class="font-serif">
                Easily deploy Laravel apps.
            </flux:heading>

            <flux:text variant="strong" size="lg" class="mt-6">
                From provisioning your server to deploying your Laravel app.
            </flux:text>

            <flux:button :href="route('register')" variant="primary" class="mt-6">{{ __('Get Started') }}</flux:button>

            <flux:heading size="lg" level="2" class="font-serif mt-20">
                The simplest pricing possible
            </flux:heading>

            <flux:text variant="strong" size="lg" class="mt-6">
                Start with a 30-day free trial. After that, it’s just $9/month for unlimited users, servers, and Laravel apps.
            </flux:text>

            <div class="mt-64 flex items-center justify-between">
                <flux:text variant="subtle" class="flex items-center gap-2">
                    <x-app-logo-icon class="size-4" />
                    <span><strong>fuse</strong>.terrific.com.mx</span>
                </flux:text>
                <flux:text variant="subtle">by <strong>Oliver Servín</strong></flux:text>
            </div>
        </flux:main>
    </body>
</html>
