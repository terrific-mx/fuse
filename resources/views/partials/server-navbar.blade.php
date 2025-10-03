<div>
    <header>
        <flux:navbar scrollable>
            <flux:navbar.item :href="route('servers.show', $server)" wire:navigate>{{ __('Overview') }}</flux:navbar.item>
            <flux:navbar.item :href="route('servers.sites.index', $server)" wire:navigate>{{ __('Sites') }}</flux:navbar.item>
        </flux:navbar>
    </header>
    <div class="-mx-8">
        <flux:separator />
    </div>
</div>
