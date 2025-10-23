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
            <section class="-mt-6 pt-10 pb-20 lg:-mt-8">
                <flux:heading level="1" size="xl">
                    Deploy Laravel apps to your own VPS—
                    <br class="hidden md:block" />
                    in minutes, not hours
                </flux:heading>
                <flux:text class="mt-2 max-w-prose">
                    Stop wrestling with server setup. With Terrific Fuse, you can provision secure, production-ready
                    servers for your Laravel apps—no manual installs, no command-line headaches, no hidden limits.
                </flux:text>
            </section>

            <section>
                <flux:heading level="2" size="lg">
                    Launch your next Laravel project—
                    <br class="hidden md:block" />
                    without the usual hassle
                </flux:heading>
                <flux:text class="mt-2 max-w-prose">
                    You want to focus on building your app, not fighting with server setup. Terrific Fuse handles the
                    heavy lifting—so you can deploy, manage, and scale your Laravel projects with confidence and ease.
                </flux:text>
                <ul class="mt-8 grid grid-cols-1 gap-6 md:grid-cols-3">
                    <li>
                        <flux:text>
                            <flux:text variant="strong" class="font-medium" inline>
                                Provision servers built for Laravel.
                            </flux:text>
                            You get everything you need—PHP (8.1–8.4), MySQL 8, Redis—pre-installed and optimized for
                            your VPS specs.
                        </flux:text>
                    </li>
                    <li>
                        <flux:text>
                            <flux:text variant="strong" class="font-medium" inline>
                                Zero downtime deployments.
                            </flux:text>
                            Ship updates from your GitHub repo with confidence. Your users never see a maintenance page.
                        </flux:text>
                    </li>
                    <li>
                        <flux:text>
                            <flux:text variant="strong" class="font-medium" inline>
                                Manage everything in a beautiful UI.
                            </flux:text>
                            Create databases, set up daemons, schedule cron jobs, configure firewalls, and restart
                            services—all without touching the terminal.
                        </flux:text>
                    </li>
                    <li>
                        <flux:text>
                            <flux:text variant="strong" class="font-medium" inline>Stay secure by default.</flux:text>
                            Terrific Fuse configures your firewall, enables automatic Ubuntu updates, disables password
                            logins, and enforces SSH key access. You always have full SSH access.
                        </flux:text>
                    </li>
                    <li>
                        <flux:text>
                            <flux:text variant="strong" class="font-medium" inline>
                                Works with any VPS provider.
                            </flux:text>
                            Use Hetzner, DigitalOcean, Vultr, or your favorite host. No vendor lock-in.
                        </flux:text>
                    </li>
                </ul>
            </section>

            <section class="mt-16">
                <flux:heading level="2" size="lg">Save time, save money, ship faster</flux:heading>
                <flux:text class="mt-2 max-w-prose">
                    Why waste hours on repetitive server tasks or pay for bloated platforms? With Terrific Fuse, you
                    automate the essentials, cut costs, and move from idea to launch in record time—even if you’re new
                    to server management.
                </flux:text>
                <ul class="mt-8 grid grid-cols-1 gap-6 md:grid-cols-3">
                    <li>
                        <flux:text>
                            <flux:text variant="strong" class="font-medium" inline>Skip the manual setup.</flux:text>
                            Stop wasting hours installing and tuning software. Terrific Fuse does it for you—even if
                            you’ve never provisioned a server before.
                        </flux:text>
                    </li>
                    <li>
                        <flux:text>
                            <flux:text variant="strong" class="font-medium" inline>
                                Unlimited servers, unlimited apps.
                            </flux:text>
                            Manage as many projects as you want. Invite your team to collaborate—no extra fees.
                        </flux:text>
                    </li>
                    <li>
                        <flux:text>
                            <flux:text variant="strong" class="font-medium" inline>Just $29/year.</flux:text>
                            Get all the power of premium platforms, without the premium price tag. If you’re not
                            delighted in your first week, request a full refund—no questions asked.
                        </flux:text>
                    </li>
                </ul>
            </section>

            <section class="mt-16">
                <flux:heading level="2" size="lg">Ideal for Laravel developers who want:</flux:heading>
                <flux:text class="mt-2 max-w-prose">
                    If you’re a Laravel developer who values control, simplicity, and security, Terrific Fuse is built
                    for you. Here’s who gets the most from our platform:
                </flux:text>
                <ul class="mt-8 grid grid-cols-1 gap-2">
                    <li>
                        <flux:text>Full control over their infrastructure</flux:text>
                    </li>
                    <li>
                        <flux:text>Effortless, secure deployments</flux:text>
                    </li>
                    <li>
                        <flux:text>A simple, affordable solution—without feature bloat or arbitrary limits</flux:text>
                    </li>
                </ul>
            </section>

            <section class="mt-16">
                <flux:heading level="3" class="mb-2">Ready to deploy your Laravel app the easy way?</flux:heading>
                <flux:button :href="route('register')" class="mb-2 max-w-prose">
                    Start your 1-week risk-free trial for just $29/year →
                </flux:button>
            </section>

            <section class="mt-10">
                <flux:text variant="strong" class="max-w-prose font-medium">
                    Even if you’ve struggled with server setup before, you’ll be up and running in minutes.
                </flux:text>
                <flux:text class="mt-2 max-w-prose">
                    You keep full control—no hidden fees, no arbitrary limits, just effortless Laravel deployments.
                </flux:text>
            </section>

            <div class="mt-64 flex items-center justify-between">
                <flux:text variant="subtle" class="flex items-center gap-2">
                    <x-app-logo-icon class="size-4" />
                    <span>
                        <strong>fuse</strong>
                        .terrific.com.mx
                    </span>
                </flux:text>
                <flux:text variant="subtle">
                    by
                    <strong>Oliver Servín</strong>
                </flux:text>
            </div>
        </flux:main>
    </body>
</html>
