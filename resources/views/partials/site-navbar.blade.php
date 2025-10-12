<div class="border-b border-zinc-200 dark:border-zinc-600">
    <flux:navbar class="-mb-px">
        <flux:navbar.item :href="route('servers.sites.show', [$server, $site])" wire:navigate>{{ __('Overview') }}</flux:navbar.item>
        <flux:navbar.item :href="route('servers.sites.deployments', [$server, $site])" wire:navigate>{{ __('Deployments') }}</flux:navbar.item>
        <flux:navbar.item :href="route('servers.sites.deployment-settings', [$server, $site])" wire:navigate>{{ __('Deployment Settings') }}</flux:navbar.item>
    </flux:navbar>
</div>
