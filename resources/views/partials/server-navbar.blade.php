<div>
    <header>
        <flux:navbar scrollable>
            <flux:navbar.item :href="route('servers.show', $server)" wire:navigate>{{ __('Overview') }}</flux:navbar.item>
            <flux:navbar.item :href="route('servers.sites.index', $server)" wire:navigate>{{ __('Sites') }}</flux:navbar.item>
            <flux:navbar.item :href="route('servers.databases', $server)" wire:navigate>{{ __('Databases') }}</flux:navbar.item>
            <flux:navbar.item :href="route('servers.cronjobs', $server)" wire:navigate>{{ __('Cronjobs') }}</flux:navbar.item>
            <flux:navbar.item :href="route('servers.daemons', $server)" wire:navigate>{{ __('Daemons') }}</flux:navbar.item>
            <flux:navbar.item :href="route('servers.firewall-rules', $server)" wire:navigate>{{ __('Firewall Rules') }}</flux:navbar.item>
            <flux:navbar.item :href="route('servers.backups', $server)" wire:navigate>{{ __('Backups') }}</flux:navbar.item>
            <flux:navbar.item :href="route('servers.services', $server)" wire:navigate>{{ __('Services') }}</flux:navbar.item>
            <flux:navbar.item :href="route('servers.files', $server)" wire:navigate>{{ __('Files') }}</flux:navbar.item>
            <flux:navbar.item :href="route('servers.logs', $server)" wire:navigate>{{ __('Logs') }}</flux:navbar.item>
        </flux:navbar>
    </header>
    <div class="-mx-8">
        <flux:separator />
    </div>
</div>
