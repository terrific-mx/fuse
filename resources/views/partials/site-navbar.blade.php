<div class="me-10 w-full pb-4 md:w-[220px]">
    <flux:navlist>
        <flux:navlist.item :href="route('servers.sites.show', [$server, $site])" wire:navigate>{{ __('Overview') }}</flux:navlist.item>
        <flux:navlist.item :href="route('servers.sites.deployments', [$server, $site])" wire:navigate>{{ __('Deployments') }}</flux:navlist.item>
    </flux:navlist>
</div>
