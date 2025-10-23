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
                <flux:heading level="1" size="xl" class="text-5xl tracking-tight text-balance font-bold!">Deploy Laravel apps, not headaches.</flux:heading>
                <flux:text class="mt-6 max-w-2xl text-lg text-pretty sm:text-xl/8">
                    Terrific Fuse saves you hours on server management, so you can focus on building. Simple, affordable
                    plans for indie devs, agencies, and startups.
                </flux:text>
            </section>

            <section class="grid grid-cols-4 gap-8">
                <flux:card class="flex flex-col">
                    <flux:heading level="2" class="mb-2 text-lg/8! font-bold!">Starter</flux:heading>
                    <div class="mt-2 flex items-baseline gap-1">
                        <flux:heading class="text-4xl! leading-tight font-extrabold!">$9</flux:heading>
                        <flux:text>/month</flux:text>
                    </div>
                    <flux:text class="mt-2">
                        For solo developers and freelancers who want fast, reliable Laravel deployments without DevOps
                        hassle.
                    </flux:text>
                    <div class="flex-1">
                        <flux:text class="mt-8 text-base min-h-[72px]" variant="strong">
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
                                <flux:text variant="strong">
                                    Core features: provisioning, deployments, SSL, backups
                                </flux:text>
                            </li>
                            <li class="flex gap-2">
                                <flux:icon.check variant="micro" class="mt-0.5" />
                                <flux:text variant="strong">Community support</flux:text>
                            </li>
                        </ul>
                    </div>
                    <flux:button variant="primary" class="mt-6 w-full">Start free trial</flux:button>
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
                        <flux:text class="mt-8 text-base min-h-[72px]" variant="strong">
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
                        <flux:text class="mt-8 text-base min-h-[72px]" variant="strong">
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

            <section class="mt-6">
                <flux:text>14-day free trial – Cancel anytime before trial ends</flux:text>
                <flux:text class="mt-1">30-day money-back guarantee – Love it or get a full refund, no questions asked</flux:text>
            </section>

            <section class="grid grid-cols-3 gap-8 mt-20">
                <blockquote>
                    <flux:text class="text-lg/8">“Built for Laravel. No DevOps experience required. Focus on code, not servers.”</flux:text>
                </blockquote>
                <blockquote>
                    <flux:text class="text-lg/8">“No more SSH headaches. One-click deploys. Peace of mind.”</flux:text>
                </blockquote>
                <blockquote>
                    <flux:text class="text-lg/8">“Growing fast? Upgrade anytime. Need more? We’ll work with you.”</flux:text>
                </blockquote>
            </section>

            <section class="mt-20">
                <flux:heading>FAQ Section</flux:heading>

                <flux:text>
                    Q: What happens if I hit my server/app/user limit? A: You can easily upgrade your plan or add
                    more capacity as you grow.
                </flux:text>
                <flux:text>Q: Can I cancel anytime? A: Yes! No contracts, no lock-in.</flux:text>

                <flux:text>Q: Do you offer discounts for annual billing? A: Yes, get 2 months free when you pay annually.</flux:text>
            </section>
        </flux:main>
    </body>
</html>
