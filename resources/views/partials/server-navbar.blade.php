<div class="border-b border-zinc-200 dark:border-zinc-600">
    <flux:navbar class="-mb-px">
        <flux:navbar.item :href="route('servers.show', $server)" wire:navigate>
            Overview
        </flux:navbar.item>
        <flux:navbar.item :href="route('servers.sites.index', $server)" wire:navigate>
            Sites
        </flux:navbar.item>
        <flux:navbar.item :href="route('servers.daemons.index', $server)" wire:navigate>
            Daemons
        </flux:navbar.item>
        <flux:navbar.item :href="route('servers.databases.index', $server)" wire:navigate>
            Databases
        </flux:navbar.item>
    </flux:navbar>
</div>
