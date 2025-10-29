<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="antialiased">
    <head>
        @include('partials.head')
    </head>
    <body>
        <flux:header container class="border-b border-zinc-200 dark:border-zinc-700">
            <flux:brand :href="route('home')" :name="config('app.name')">
                <x-slot:logo>
                    <x-logo class="h-6" />
                </x-slot:logo>
            </flux:brand>

            <flux:navbar class="-mb-px">
                <flux:navbar.item :href="route('pricing')" wire:navigate>Pricing</flux:navbar.item>
                <flux:navbar.item href="https://github.com/antihq/fuse">Github</flux:navbar.item>
            </flux:navbar>

            <flux:spacer />

            <div class="flex gap-2">
                <flux:button :href="route('dashboard')" variant="subtle" size="sm">Account</flux:button>
            </div>
        </flux:header>

        <flux:main container>
            {{ $slot }}
        </flux:main>

        <flux:footer container>
            <div>
                <flux:text class="text-sm/6">
                    Built with
                    <flux:icon.heart variant="micro" class="inline" />
                    by
                    <flux:link href="https://x.com/oliverservinX" :accent="false">Oliver Servín</flux:link>
                </flux:text>
                <flux:text class="mt-6 lg:mt-8 text-sm/6">
                    &copy; {{ date('Y') }} Anti Software. All rights reserved. Problems or questions? Contact <
                    <flux:link href="mailto:support@antihq.com" :accent="false">support@antihq.com</flux:link>
                    >.
                </flux:text>
            </div>
        </flux:footer>

        @fluxScripts
    </body>
</html>
