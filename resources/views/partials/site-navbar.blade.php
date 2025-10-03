<div>
    <header>
        <flux:navbar scrollable>
            <flux:navbar.item :href="route('servers.sites.show', [$server, $site])" wire:navigate>{{ __('Overview') }}</flux:navbar.item>
            <flux:navbar.item :href="route('servers.sites.deployments', [$server, $site])" wire:navigate>{{ __('Deployments') }}</flux:navbar.item>
        </flux:navbar>
    </header>
    <div class="-mx-8">
        <flux:separator />
    </div>
</div>
