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
            <section class="-mt-6 pt-24 sm:pt-32 lg:-mt-8">
                <flux:heading level="1" size="xl" class="text-5xl font-bold! tracking-tight text-balance sm:text-6xl">
                    Deploy Laravel apps, not headaches
                </flux:heading>
                <flux:text class="mt-6 max-w-2xl text-lg font-medium text-pretty sm:text-xl/8">
                    Terrific Fuse saves you hours on server management, so you can focus on building. Simple, affordable
                    plans for indie devs, agencies, and startups.
                </flux:text>
            </section>

            <section
                class="isolate mx-auto mt-16 grid max-w-md grid-cols-1 gap-8 md:max-w-2xl md:grid-cols-2 lg:max-w-4xl xl:mx-0 xl:max-w-none xl:grid-cols-4"
            >
                <flux:card class="flex flex-col p-8">
                    <flux:heading level="3" class="text-lg/8! font-semibold!">Starter</flux:heading>
                    <div class="mt-6 flex items-baseline gap-1">
                        <flux:heading class="text-4xl! leading-tight font-extrabold!">$9</flux:heading>
                        <flux:text class="text-sm/6 font-semibold">/month</flux:text>
                    </div>
                    <flux:text class="mt-3 text-sm/6">
                        For solo developers and freelancers who want fast, reliable Laravel deployments without DevOps
                        hassle.
                    </flux:text>
                    <flux:button variant="primary" class="mt-6 w-full">Start free trial</flux:button>
                    <flux:text variant="strong" class="mt-6 text-sm/6 font-semibold">
                        Perfect for getting started or running side projects.
                    </flux:text>

                    <ul class="mt-4 flex-1 space-y-3">
                        <li class="flex gap-2">
                            <flux:icon.check-circle variant="mini" class="text-accent mt-0.5" />
                            <flux:text class="space-y-3 text-sm/6">Manage up to 2 servers and 5 apps</flux:text>
                        </li>
                        <li class="flex gap-2">
                            <flux:icon.check-circle variant="mini" class="text-accent mt-0.5" />
                            <flux:text class="space-y-3 text-sm/6">1 user</flux:text>
                        </li>
                        <li class="flex gap-2">
                            <flux:icon.check-circle variant="mini" class="text-accent mt-0.5" />
                            <flux:text class="space-y-3 text-sm/6">
                                Core features: provisioning, deployments, SSL, backups
                            </flux:text>
                        </li>
                        <li class="flex gap-2">
                            <flux:icon.check-circle variant="mini" class="text-accent mt-0.5" />
                            <flux:text class="space-y-3 text-sm/6">Community support</flux:text>
                        </li>
                    </ul>
                </flux:card>
                <flux:card class="flex flex-col">
                    <flux:heading level="2" class="mb-2 text-lg/8! font-bold!">Team</flux:heading>
                    <div class="mt-2 flex items-baseline gap-1">
                        <flux:heading class="text-4xl! leading-tight font-extrabold!">$19</flux:heading>
                        <flux:text>/month</flux:text>
                    </div>
                    <flux:text class="mt-2 min-h-[80px]">
                        For small teams and agencies managing multiple projects and collaborating with others.
                    </flux:text>
                    <div class="flex-1">
                        <flux:text class="mt-8 min-h-[72px] text-base" variant="strong">
                            Built for teams who want to move fast and work together.
                        </flux:text>
                        <ul class="mt-4 space-y-3">
                            <li class="flex gap-2">
                                <flux:icon.check variant="micro" class="mt-0.5" />
                                <flux:text variant="strong">Manage up to 10 servers and 20 apps</flux:text>
                            </li>
                            <li class="flex gap-2">
                                <flux:icon.check variant="micro" class="mt-0.5" />
                                <flux:text variant="strong">Up to 3 users</flux:text>
                            </li>
                            <li class="flex gap-2">
                                <flux:icon.check variant="micro" class="mt-0.5" />
                                <flux:text variant="strong">Team management, zero-downtime deploys</flux:text>
                            </li>
                            <li class="flex gap-2">
                                <flux:icon.check variant="micro" class="mt-0.5" />
                                <flux:text variant="strong">Email support</flux:text>
                            </li>
                        </ul>
                    </div>
                    <flux:button variant="primary" class="mt-6 w-full">Start free trial</flux:button>
                </flux:card>
                <flux:card class="flex flex-col">
                    <flux:heading level="2" class="mb-2 text-lg/8! font-bold!">Agency</flux:heading>
                    <div class="mt-2 flex items-baseline gap-1">
                        <flux:heading class="text-4xl! leading-tight font-extrabold!">$29</flux:heading>
                        <flux:text>/month</flux:text>
                    </div>
                    <flux:text class="mt-2">
                        For growing agencies and startups who need advanced features, more capacity, and priority
                        support.
                    </flux:text>
                    <div class="flex-1">
                        <flux:text class="mt-8 text-base" variant="strong">
                            Scale your agency with powerful tools and responsive support.
                        </flux:text>
                        <ul class="mt-4 space-y-3">
                            <li class="flex gap-2">
                                <flux:icon.check variant="micro" class="mt-0.5" />
                                <flux:text variant="strong">Manage up to 25 servers and 50 apps</flux:text>
                            </li>
                            <li class="flex gap-2">
                                <flux:icon.check variant="micro" class="mt-0.5" />
                                <flux:text variant="strong">Up to 10 users</flux:text>
                            </li>
                            <li class="flex gap-2">
                                <flux:icon.check variant="micro" class="mt-0.5" />
                                <flux:text variant="strong">All features, API access, advanced config</flux:text>
                            </li>
                            <li class="flex gap-2">
                                <flux:icon.check variant="micro" class="mt-0.5" />
                                <flux:text variant="strong">Priority support</flux:text>
                            </li>
                        </ul>
                    </div>
                    <flux:button variant="primary" class="mt-6 w-full">Start free trial</flux:button>
                </flux:card>
                <flux:card class="flex flex-col">
                    <flux:heading level="2" class="mb-2 text-lg/8! font-bold!">Enterprise</flux:heading>
                    <div class="mt-2 flex items-baseline gap-1">
                        <flux:heading class="text-4xl! leading-tight font-extrabold!">Custom</flux:heading>
                    </div>
                    <flux:text class="mt-2 min-h-[80px]">
                        For organizations with large teams or special requirements. Let’s talk!
                    </flux:text>
                    <div class="flex-1">
                        <flux:text class="mt-8 min-h-[72px] text-base" variant="strong">
                            Need more? We’ll tailor a plan for you.
                        </flux:text>
                        <ul class="mt-4 space-y-3">
                            <li class="flex gap-2">
                                <flux:icon.check variant="micro" class="mt-0.5" />
                                <flux:text variant="strong">Custom limits</flux:text>
                            </li>
                            <li class="flex gap-2">
                                <flux:icon.check variant="micro" class="mt-0.5" />
                                <flux:text variant="strong">Dedicated onboarding, custom SLAs</flux:text>
                            </li>
                        </ul>
                    </div>
                    <flux:button variant="primary" class="mt-6 w-full">Contact us</flux:button>
                </flux:card>
            </section>

            <section class="mt-8 grid grid-cols-12 gap-8 rounded-xl bg-zinc-50 p-8 sm:p-10">
                <div class="col-span-5">
                    <flux:heading level="h2" class="text-lg/8! font-bold!">Add-ons</flux:heading>
                </div>

                <div class="col-span-7">
                    <div class="pb-6">
                        <flux:text variant="strong" class="text-base/7 font-semibold">
                            Extra servers/apps/users
                        </flux:text>
                        <flux:text class="mt-2 text-base/7">
                            $2 per server/app/user per month (for Team/Agency).
                        </flux:text>
                    </div>

                    <flux:separator />

                    <div class="pt-6">
                        <flux:text variant="strong" class="text-base/7 font-semibold">Concierge onboarding</flux:text>
                        <flux:text class="mt-2 text-base/7">$99 one-time (Agency/Enterprise only).</flux:text>
                    </div>
                </div>
            </section>

            <section class="mt-8">
                <ul class="space-y-3">
                    <li class="flex gap-2">
                        <flux:icon.check variant="micro" class="mt-0.5" />
                        <flux:text variant="strong">14-day free trial – Cancel anytime before trial ends</flux:text>
                    </li>
                    <li class="flex gap-2">
                        <flux:icon.check variant="micro" class="mt-0.5" />
                        <flux:text variant="strong">
                            30-day money-back guarantee – Love it or get a full refund, no questions asked
                        </flux:text>
                    </li>
                </ul>
            </section>

            <section class="mt-24 grid grid-cols-3 gap-8 sm:mt-56">
                <blockquote class="rounded-xl bg-blue-50 p-8">
                    <flux:text variant="strong" class="text-lg/8">
                        “Built for Laravel. No DevOps experience required. Focus on code, not servers.”
                    </flux:text>
                </blockquote>
                <blockquote class="rounded-xl bg-blue-50 p-8">
                    <flux:text variant="strong" class="text-lg/8">
                        “No more SSH headaches. One-click deploys. Peace of mind.”
                    </flux:text>
                </blockquote>
                <blockquote class="rounded-xl bg-blue-50 p-8">
                    <flux:text variant="strong" class="text-lg/8">
                        “Growing fast? Upgrade anytime. Need more? We’ll work with you.”
                    </flux:text>
                </blockquote>
            </section>

            <section class="mt-24 grid grid-cols-12 gap-8 sm:mt-56">
                <div class="col-span-5">
                    <flux:heading level="h2" class="text-4xl font-semibold tracking-tight sm:text-5xl">
                        Frequently asked questions
                    </flux:heading>
                </div>

                <div class="col-span-7">
                    <div class="pb-6">
                        <flux:text variant="strong" class="text-base/7 font-semibold">
                            What happens if I hit my server/app/user limit?
                        </flux:text>
                        <flux:text class="mt-2 text-base/7">
                            You can easily upgrade your plan or add more capacity as you grow.
                        </flux:text>
                    </div>

                    <flux:separator />

                    <div class="py-6">
                        <flux:text variant="strong" class="text-base/7 font-semibold">Can I cancel anytime?</flux:text>
                        <flux:text class="mt-2 text-base/7">Yes! No contracts, no lock-in.</flux:text>
                    </div>

                    <flux:separator />

                    <div class="pt-6">
                        <flux:text variant="strong" class="text-base/7 font-semibold">
                            Do you offer discounts for annual billing?
                        </flux:text>
                        <flux:text class="mt-2 text-base/7">Yes, get 2 months free when you pay annually.</flux:text>
                    </div>
                </div>
            </section>
        </flux:main>
    </body>
</html>
