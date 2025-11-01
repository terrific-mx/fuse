<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="lg:bg-zinc-100 dark:bg-zinc-900 dark:lg:bg-zinc-950 dark antialiased">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen bg-white lg:bg-zinc-100 dark:bg-zinc-900 dark:lg:bg-zinc-950">
        <flux:header class="lg:border-b border-zinc-200 dark:border-zinc-700">
            <flux:sidebar.toggle class="lg:hidden" icon="bars-2" inset="left" size="sm" />

            <a href="{{ route('home') }}" class="max-lg:hidden mr-5">
                <x-logo class="h-6" />
            </a>

            @auth
                <div class="max-lg:hidden flex items-center h-full">
                    <livewire:organizations-dropdown />
                    <flux:separator vertical class="my-5 mx-1" />
                </div>
            @endauth

            <flux:navbar class="-mb-px max-lg:hidden">
                @auth
                    <flux:navbar.item :href="route('dashboard')" :accent="false" wire:navigate>Home</flux:navbar.item>
                    <flux:navbar.item :href="route('servers.index')" :accent="false" wire:navigate>Servers</flux:navbar.item>
                    <flux:navbar.item :href="route('ssh-keys.index')" :accent="false" wire:navigate>SSH keys</flux:navbar.item>
                @else
                    <flux:navbar.item :href="route('pricing')" wire:navigate>Pricing</flux:navbar.item>
                    <flux:navbar.item href="https://github.com/antihq/fuse">Github</flux:navbar.item>
                @endauth
            </flux:navbar>

            <flux:spacer />

            <!-- Desktop User Menu -->
            @auth
                <flux:dropdown position="top" align="end">
                    <flux:button size="sm" variant="ghost" square>
                        <flux:avatar size="xs" :name="Auth::user()->name" color="auto" initials:single />
                    </flux:button>

                    <flux:menu>
                        <flux:menu.item :href="route('settings.profile')" icon="cog-8-tooth" icon:variant="micro" wire:navigate>{{ __('Settings') }}</flux:menu.item>

                        <flux:menu.separator />

                        <form method="POST" action="{{ route('logout') }}" class="w-full">
                            @csrf
                            <flux:menu.item as="button" type="submit" icon="arrow-right-start-on-rectangle" icon:variant="micro" class="w-full">
                                {{ __('Log Out') }}
                            </flux:menu.item>
                        </form>
                    </flux:menu>
                </flux:dropdown>
            @else
                <flux:button :href="route('dashboard')" variant="subtle">Account</flux:button>
            @endauth
        </flux:header>

        <!-- Mobile Menu -->
        <flux:sidebar stashable sticky class="lg:hidden border-e border-zinc-200 bg-white dark:border-zinc-700 dark:bg-zinc-900">
            <flux:sidebar.header>
                <a href="{{ route('home') }}" class="ms-2"><x-logo class="h-6" /></a>
                <flux:sidebar.collapse class="in-data-flux-sidebar-on-desktop:not-in-data-flux-sidebar-collapsed-desktop:-mr-2" />
            </flux:sidebar.header>

            @auth
                <livewire:organizations-dropdown />
            @endauth

            <flux:separator variant="subtle" />

            <flux:sidebar.nav>
                @auth
                    <flux:sidebar.item :href="route('dashboard')" :accent="false" wire:navigate>{{ __('Home') }}</flux:sidebar.item>
                    <flux:sidebar.item :href="route('servers.index')" :accent="false" wire:navigate>{{ __('Servers') }}</flux:sidebar.item>
                    <flux:sidebar.item :href="route('ssh-keys.index')" :accent="false" wire:navigate>{{ __('SSH keys') }}</flux:sidebar.item>
                @else
                    <flux:sidebar.item :href="route('pricing')" wire:navigate>Pricing</flux:sidebar.item>
                    <flux:sidebar.item href="https://github.com/antihq/fuse">Github</flux:sidebar.item>
                @endauth
            </flux:sidebar.nav>
        </flux:sidebar>

        {{ $slot }}

        <flux:footer class="border-t border-zinc-200">
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
