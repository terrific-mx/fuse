<header class="border-b border-zinc-200 dark:border-zinc-700">
    <flux:navbar scrollable>
        <flux:navbar.item :href="route('servers.show', $server)">{{ __('Overview') }}</flux:navbar.item>
        <flux:navbar.item :href="route('servers.sites.index', $server)">{{ __('Sites') }}</flux:navbar.item>
        <flux:navbar.item :href="route('servers.databases.index', $server)">{{ __('Databases') }}</flux:navbar.item>
        <flux:navbar.item href="#">{{ __('Cronjobs') }}</flux:navbar.item>
        <flux:navbar.item href="#">{{ __('Deamons') }}</flux:navbar.item>
        <flux:navbar.item href="#">{{ __('Firewall Rules') }}</flux:navbar.item>
        <flux:navbar.item href="#">{{ __('Backups') }}</flux:navbar.item>
        <flux:navbar.item href="#">{{ __('Software') }}</flux:navbar.item>
        <flux:navbar.item href="#">{{ __('Software') }}</flux:navbar.item>
        <flux:navbar.item href="#">{{ __('Files') }}</flux:navbar.item>
        <flux:navbar.item href="#">{{ __('Logs') }}</flux:navbar.item>
    </flux:navbar>
</header>
