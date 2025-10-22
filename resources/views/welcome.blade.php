<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="antialiased">
    <head>
        @include('partials.head')
    </head>
    <body>
        <flux:header container class="border-b border-zinc-200 dark:border-zinc-700">
            <flux:brand href="route('dashboard')" :name="config('app.name')" class="[&>div]:first:hidden" />

            <flux:navbar class="-mb-px">
                <flux:navbar.item href="https://github.com/terrific-mx/fuse">Github</flux:navbar.item>
            </flux:navbar>

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

        <flux:main container>
            <section class="pt-10 pb-20 -mt-6 lg:-mt-8">
                <flux:heading level="1" size="xl" class="text-7xl/20 font-bold! tracking-tight">
                    Easily deploy
                    <br class="hidden md:block" />
                    Laravel apps
                </flux:heading>

                <flux:text class="mt-3 text-xl/8">
                    From provisioning your server to deploying your Laravel app.
                </flux:text>

                <flux:button :href="route('register')" variant="primary" color="zinc" icon:trailing="arrow-right" class="mt-8 h-12">
                    Sign up for $29 / year
                </flux:button>
            </section>

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
