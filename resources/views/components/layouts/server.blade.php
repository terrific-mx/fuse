<div class="max-w-3xl mx-auto">
    <header class="flex items-center">
        <div class="flex items-center gap-3">
            <flux:avatar :name="strtoupper($server->name)" color="auto" initials:single :color:seed="$server->id" />
            <flux:heading class="text-xl">{{ $server->name }}</flux:heading>
        </div>
        <flux:spacer />
        <flux:dropdown align="end">
            <flux:button icon:trailing="ellipsis-horizontal" size="sm" variant="subtle" />

            <flux:menu>
                <flux:menu.item :href="route('servers.passwords', $server)" wire:navigate icon="key" icon:variant="micro">
                    Passwords
                </flux:menu.item>
            </flux:menu>
        </flux:dropdown>
    </header>

    <flux:text class="mt-4">
        {{ $server->ip_address }}
    </flux:text>

    <flux:spacer class="mt-8" />

    <div class="border-b border-zinc-200 dark:border-zinc-700">
        <flux:navbar class="-mb-px overflow-hidden overflow-x-scroll">
            <flux:navbar.item :href="route('servers.show', $server)" :accent="false" wire:navigate>Home</flux:navbar.item>
            <flux:navbar.item :href="route('servers.sites.index', $server)" :accent="false" wire:navigate>Sites</flux:navbar.item>
            <flux:navbar.item :href="route('servers.databases.index', $server)" :accent="false" wire:navigate>Databases</flux:navbar.item>
            <flux:navbar.item :href="route('servers.cronjobs.index', $server)" :accent="false" wire:navigate>Cronjobs</flux:navbar.item>
            <flux:navbar.item :href="route('servers.firewall-rules.index', $server)" :accent="false" wire:navigate>Firewall rules</flux:navbar.item>
            <flux:navbar.item :href="route('servers.daemons.index', $server)" :accent="false" wire:navigate>Daemons</flux:navbar.item>
        </flux:navbar>
    </div>

    <flux:spacer class="mt-10" />

    {{ $slot }}
</div>
