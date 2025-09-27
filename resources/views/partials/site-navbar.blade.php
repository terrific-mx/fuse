<div>
    <header>
        <flux:navbar scrollable>
            <flux:navbar.item :href="route('servers.sites.show', [$server, $site])" wire:navigate>{{ __('Overview') }}</flux:navbar.item>
            <flux:navbar.item :href="route('servers.sites.deployments', [$server, $site])" wire:navigate>{{ __('Deployments') }}</flux:navbar.item>
            <flux:navbar.item :href="route('servers.sites.site-settings', [$server, $site])" wire:navigate>{{ __('Site Settings') }}</flux:navbar.item>
            <flux:navbar.item :href="route('servers.sites.deployment-settings', [$server, $site])" wire:navigate>{{ __('Deployment Settings') }}</flux:navbar.item>
            <flux:navbar.item :href="route('servers.sites.ssl', [$server, $site])" wire:navigate>{{ __('SSL') }}</flux:navbar.item>
            <flux:navbar.item :href="route('servers.sites.files', [$server, $site])" wire:navigate>{{ __('Files') }}</flux:navbar.item>
            <flux:navbar.item :href="route('servers.sites.logs', [$server, $site])" wire:navigate>{{ __('Logs') }}</flux:navbar.item>
        </flux:navbar>
    </header>
    <div class="-mx-8">
        <flux:separator />
    </div>
</div>
