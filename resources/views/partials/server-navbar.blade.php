<header class="border-b border-zinc-200 dark:border-zinc-700">
    <flux:navbar scrollable>
        <flux:navbar.item :href="route('servers.show', $server)" wire:navigate>{{ __('Overview') }}</flux:navbar.item>
        <flux:navbar.item :href="route('servers.sites.index', $server)" wire:navigate>{{ __('Sites') }}</flux:navbar.item>
        <flux:navbar.item :href="route('servers.databases.index', $server)" wire:navigate>{{ __('Databases') }}</flux:navbar.item>
        <flux:navbar.item :href="route('servers.cronjobs.index', $server)" wire:navigate>{{ __('Cronjobs') }}</flux:navbar.item>
        <flux:navbar.item :href="route('servers.daemons.index', $server)" wire:navigate>{{ __('Daemons') }}</flux:navbar.item>
        <flux:navbar.item :href="route('servers.firewall-rules', $server)" wire:navigate>{{ __('Firewall Rules') }}</flux:navbar.item>
        <flux:navbar.item href="#">{{ __('Backups') }}</flux:navbar.item>
        <flux:navbar.item href="#">{{ __('Software') }}</flux:navbar.item>
        <flux:navbar.item href="#">{{ __('Files') }}</flux:navbar.item>
        <flux:navbar.item href="#">{{ __('Logs') }}</flux:navbar.item>
    </flux:navbar>
</header>
