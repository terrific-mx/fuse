<flux:navbar>
    <flux:navbar.item :href="route('servers.show', $server)" :accent="false" wire:navigate>
        Overview
    </flux:navbar.item>
    <flux:navbar.item :href="route('servers.sites.index', $server)" :accent="false" wire:navigate>
        Sites
    </flux:navbar.item>
    <flux:navbar.item :href="route('servers.databases.index', $server)" :accent="false" wire:navigate>
        Databases
    </flux:navbar.item>
    <flux:navbar.item :href="route('servers.cronjobs.index', $server)" :accent="false" wire:navigate>
        Cronjobs
    </flux:navbar.item>
    <flux:navbar.item :href="route('servers.firewall-rules.index', $server)" :accent="false" wire:navigate>
        Firewall Rules
    </flux:navbar.item>
    <flux:navbar.item :href="route('servers.daemons.index', $server)" :accent="false" wire:navigate>
        Daemons
    </flux:navbar.item>
    <flux:navbar.item :href="route('servers.services.index', $server)" :accent="false" wire:navigate>
        Services
    </flux:navbar.item>
</flux:navbar>
