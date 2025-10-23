<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="antialiased">
    <head>
        @include('partials.head')
    </head>
    <body>
        <flux:header container class="border-b border-zinc-200 dark:border-zinc-700">
            <flux:brand href="route('dashboard')" :name="config('app.name')" class="[&>div]:first:hidden" />

            <flux:navbar class="-mb-px">
                <flux:navbar.item :href="route('pricing')">Pricing</flux:navbar.item>
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
            <section class="-mt-6 pt-10 pb-20 lg:-mt-8">
                <flux:heading level="1" size="xl">Deploy Laravel apps, not headaches.</flux:heading>
                <flux:text>
                    Terrific Fuse saves you hours on server management, so you can focus on building. Simple, affordable
                    plans for indie devs, agencies, and startups.
                </flux:text>
            </section>

            <section class="grid grid-cols-4">
                <flux:card>
                    <flux:heading level="2" class="mb-2 text-lg/8! font-bold!">Starter</flux:heading>
                    <div class="mt-2 flex items-baseline gap-1">
                        <flux:heading class="text-4xl! leading-tight font-extrabold!">$9</flux:heading>
                        <flux:text>/month</flux:text>
                    </div>
                    <flux:text class="mt-2">
                        For solo developers and freelancers who want fast, reliable Laravel deployments without DevOps hassle.
                    </flux:text>
                    <flux:text class="mt-8 text-base" variant="strong">
                        Perfect for getting started or running side projects.
                    </flux:text>
                    <ul class="mt-4 space-y-3">
                        <li class="flex gap-2">
                            <flux:icon.check variant="micro" class="mt-0.5" />
                            <flux:text variant="strong">Manage up to 2 servers and 5 apps</flux:text>
                        </li>
                        <li class="flex gap-2">
                            <flux:icon.check variant="micro" class="mt-0.5" />
                            <flux:text variant="strong">1 user</flux:text>
                        </li>
                        <li class="flex gap-2">
                            <flux:icon.check variant="micro" class="mt-0.5" />
                            <flux:text variant="strong">Core features: provisioning, deployments, SSL, backups</flux:text>
                        </li>
                        <li class="flex gap-2">
                            <flux:icon.check variant="micro" class="mt-0.5" />
                            <flux:text variant="strong">Community support</flux:text>
                        </li>
                    </ul>
                    <flux:button variant="primary" class="w-full mt-6">Start free trial</flux:button>
                </flux:card>
            </section>
        </flux:main>
    </body>
</html>
