<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="antialiased">
    <head>
        @include('partials.head')
    </head>
    <body>
        <flux:header container class="border-b border-zinc-200 dark:border-zinc-700">
            <flux:brand :href="route('home')" :name="config('app.name')" class="[&>div]:first:hidden" />

            <flux:navbar class="-mb-px">
                <flux:navbar.item :href="route('pricing')">Pricing</flux:navbar.item>
                <flux:navbar.item href="https://github.com/antihq/fuse">Github</flux:navbar.item>
            </flux:navbar>

            <flux:spacer />

            <div class="flex gap-2">
                <flux:button :href="route('dashboard')" variant="subtle" size="sm">Account</flux:button>
            </div>
        </flux:header>

        <flux:main container>
            <section class="-mt-6 max-w-2xl py-32 sm:py-48 lg:-mt-8 lg:py-56">
                <flux:heading level="1" class="text-5xl! font-semibold tracking-tight text-balance sm:text-7xl!">
                    Stop Wrestling with Server Setup. Deploy Laravel Apps in Minutes—Not Hours
                </flux:heading>
                <flux:text class="mt-8 text-lg font-medium text-pretty sm:text-xl/8">
                    You want to launch your Laravel app, not waste time configuring servers. With Antifuse, you can
                    provision a secure, production-ready VPS for Laravel in just a few clicks—no manual installs, no
                    command-line headaches, no guesswork.
                </flux:text>
                <div class="mt-10 flex items-center gap-x-2">
                    <flux:button :href="route('register')" variant="primary" class="font-semibold">
                        Start free trial
                    </flux:button>
                    <flux:button
                        :href="route('pricing')"
                        variant="ghost"
                        icon:trailing="arrow-right"
                        class="font-semibold"
                    >
                        Pricing
                    </flux:button>
                </div>
            </section>

            <section class="py-24 sm:py-32">
                <div class="max-w-2xl">
                    <flux:heading level="2" class="text-4xl! font-semibold tracking-tight text-pretty sm:text-5xl!">
                        Get Back to Building—We’ll Handle the Ops
                    </flux:heading>
                </div>
                <div
                    class="mt-16 grid max-w-2xl grid-cols-1 gap-8 sm:grid-cols-2 lg:mx-0 lg:max-w-none lg:grid-cols-3 lg:gap-x-16"
                >
                    <div class="relative pl-9">
                        <flux:text variant="strong" class="text-base/7 font-semibold" inline>
                            <flux:icon name="server" class="text-accent absolute top-1 left-1 size-5" />
                            Spin up a Laravel-ready server in just a few minutes.
                        </flux:text>
                        <flux:text class="text-base/7" inline>
                            Antifuse automatically installs and configures everything you need: PHP (8.1–8.4), MySQL 8,
                            Redis, and more.
                        </flux:text>
                    </div>
                    <div class="relative pl-9">
                        <flux:text variant="strong" class="text-base/7 font-semibold" inline>
                            <flux:icon name="folder-git-2" class="text-accent absolute top-1 left-1 size-5" />
                            Deploy from GitHub with zero downtime.
                        </flux:text>
                        <flux:text class="text-base/7" inline>
                            Push your code, and your app goes live—smoothly, every time.
                        </flux:text>
                    </div>
                    <div class="relative pl-9">
                        <flux:text variant="strong" class="text-base/7 font-semibold" inline>
                            <flux:icon name="sparkles" class="text-accent absolute top-1 left-1 size-5" />
                            Optimize for your VPS, automatically.
                        </flux:text>
                        <flux:text class="text-base/7" inline>
                            PHP and MySQL settings are tuned to your server’s specs, so you get peak performance without
                            lifting a finger.
                        </flux:text>
                    </div>
                    <div class="relative pl-9">
                        <flux:text variant="strong" class="text-base/7 font-semibold" inline>
                            <flux:icon name="computer-desktop" class="text-accent absolute top-1 left-1 size-5" />
                            Manage everything from a clean UI.
                        </flux:text>
                        <flux:text class="text-base/7" inline>
                            Create databases, set up daemons, schedule cron jobs, tweak firewall rules, and restart
                            services—all without touching the terminal.
                        </flux:text>
                    </div>
                    <div class="relative pl-9">
                        <flux:text variant="strong" class="text-base/7 font-semibold" inline>
                            <flux:icon name="shield-check" class="text-accent absolute top-1 left-1 size-5" />
                            Stay secure, always.
                        </flux:text>
                        <flux:text class="text-base/7" inline>
                            Firewalls are locked down, SSH password logins are disabled, and only your SSH keys grant
                            access. Automated Ubuntu updates keep your server patched—even if you forget.
                        </flux:text>
                    </div>
                    <div class="relative pl-9">
                        <flux:text variant="strong" class="text-base/7 font-semibold" inline>
                            <flux:icon name="globe-alt" class="text-accent absolute top-1 left-1 size-5" />
                            Works with any VPS provider.
                        </flux:text>
                        <flux:text class="text-base/7" inline>
                            Use Hetzner, DigitalOcean, Vultr, or your favorite host. You’re in control.
                        </flux:text>
                    </div>
                </div>
            </section>

            <section class="py-24 sm:py-32">
                <div
                    class="mx-auto grid max-w-2xl grid-cols-1 gap-x-8 gap-y-16 sm:gap-y-20 lg:mx-0 lg:max-w-none lg:grid-cols-2 lg:items-start"
                >
                    <div class="lg:pr-4">
                        <div class="lg:max-w-lg">
                            <flux:heading
                                level="2"
                                class="text-4xl! font-semibold tracking-tight text-pretty sm:text-5xl!"
                            >
                                Ideal for Indie Developers, Freelancers, Small Agencies, and Startups
                            </flux:heading>
                        </div>
                    </div>
                    <div class="md:-ml-4 lg:ml-0 lg:max-w-lg">
                        <flux:text class="text-lg/8">
                            You’re building Laravel apps for clients or your own projects. You want to move fast, stay
                            secure, and avoid DevOps rabbit holes. Antifuse is for you—even if you’ve never managed a
                            server before.
                        </flux:text>

                        <blockquote class="mt-16 border-l border-zinc-200 pl-8 dark:border-zinc-700">
                            <flux:text class="text-base/7">
                                “I built Antifuse for myself, then opened it to the public so anyone can deploy Laravel
                                apps with ease. It’s fully open source—so you can trust, audit, and contribute.”
                            </flux:text>

                            <div class="mt-6">
                                <flux:text variant="strong" class="text-sm/6 font-semibold" inline>
                                    Oliver Servín –
                                </flux:text>
                                <flux:text class="text-sm/6" inline>Creator</flux:text>
                            </div>
                        </blockquote>
                    </div>
                </div>
            </section>

            <section class="py-24 sm:py-32">
                <div class="rounded-3xl bg-zinc-50 px-6 py-24 sm:px-16 dark:bg-zinc-900">
                    <div class="max-w-2xl">
                        <flux:heading level="2" class="text-4xl! font-semibold tracking-tight text-pretty sm:text-5xl!">
                            Invite Your Team—Collaborate with Confidence
                        </flux:heading>
                        <flux:text class="mt-6 text-lg/8">
                            On the Team & Agency plan, you can invite colleagues to manage servers and apps together. No
                            more sharing root passwords or juggling access.
                        </flux:text>
                    </div>
                </div>
            </section>

            <section class="py-24 sm:py-32">
                <div
                    class="mx-auto grid max-w-2xl grid-cols-1 gap-x-8 gap-y-16 sm:gap-y-20 lg:mx-0 lg:max-w-none lg:grid-cols-2 lg:items-start"
                >
                    <div class="lg:pr-4">
                        <div class="lg:max-w-lg">
                            <flux:heading
                                level="2"
                                class="text-4xl! font-semibold tracking-tight text-pretty sm:text-5xl!"
                            >
                                Try It Free for 14 Days
                            </flux:heading>
                        </div>
                    </div>
                    <div class="space-y-8 md:-ml-4 lg:ml-0 lg:max-w-lg">
                        <div class="relative pl-9">
                            <flux:text variant="strong" class="text-base/7 font-semibold" inline>
                                <flux:icon.check class="text-accent absolute top-1 left-1 size-5" />
                                14-day free trial.
                            </flux:text>
                            <flux:text class="text-base/7" inline>
                                Experience effortless Laravel deployments. You’ll need a credit card to begin, but you
                                won’t be charged unless you continue after the trial.
                            </flux:text>
                        </div>
                        <div class="relative pl-9">
                            <flux:text variant="strong" class="text-base/7 font-semibold" inline>
                                <flux:icon.check class="text-accent absolute top-1 left-1 size-5" />
                                30-day money-back guarantee.
                            </flux:text>
                            <flux:text class="text-base/7" inline>
                                Love it or get a full refund, even if you’ve already paid.
                            </flux:text>
                        </div>
                    </div>
                </div>
            </section>

            <div class="py-24 sm:py-32">
                <div class="rounded-3xl bg-blue-100 px-6 py-24 sm:px-16 dark:bg-blue-950">
                    <flux:heading
                        level="2"
                        class="max-w-2xl text-4xl! font-semibold tracking-tight text-balance sm:text-5xl!"
                    >
                        Ready to launch your next Laravel app—without the server stress?
                    </flux:heading>
                    <div class="mt-10 flex items-center gap-x-2">
                        <flux:button :href="route('register')" variant="primary" class="font-semibold">
                            Start free trial
                        </flux:button>
                    </div>
                </div>
            </div>

            <footer class="mt-24 sm:mt-56">
                <div class="pb-14 sm:pb-16">
                    <flux:text class="text-sm/6">
                        Built with
                        <flux:icon.heart variant="micro" class="inline" />
                        by
                        <flux:link href="https://x.com/oliverservinX" :accent="false">Oliver Servín</flux:link>
                    </flux:text>
                    <flux:text class="mt-10 text-sm/6">
                        &copy; {{ date('Y') }} Anti Software. All rights reserved. Problems or questions? Contact <
                        <flux:link href="mailto:support@antihq.com" :accent="false">support@antihq.com</flux:link>
                        >.
                    </flux:text>
                </div>
            </footer>
        </flux:main>
    </body>
</html>
