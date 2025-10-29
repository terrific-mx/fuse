<?php

use App\Models\Server;
use App\Models\Site;
use Livewire\Volt\Component;

new class extends Component
{
    public Server $server;

    public Site $site;

    public function mount()
    {
        $this->authorize('view', $this->site);
    }
}; ?>

<div>
    <flux:breadcrumbs>
        <flux:breadcrumbs.item :href="route('servers.index')" wire:navigate>Servers</flux:breadcrumbs.item>
        <flux:breadcrumbs.item :href="route('servers.show', $server)" wire:navigate>
            {{ $server->name }}
        </flux:breadcrumbs.item>
        <flux:breadcrumbs.item :href="route('servers.sites.index', $server)" wire:navigate>
            Sites
        </flux:breadcrumbs.item>
    </flux:breadcrumbs>

    <flux:spacer class="mt-8" />

    <flux:heading size="xl">{{ $site->hostname }}</flux:heading>

    <div class="isolate mt-2.5 flex flex-wrap justify-between gap-x-6 gap-y-4">
        <div class="flex flex-wrap gap-x-10 gap-y-4 py-1.5">
            <flux:text variant="strong" class="flex items-center gap-3" inline>
                <flux:icon.server variant="micro" class="fill-zinc-400 dark:fill-zinc-500" />
                {{ $site->repository_url }}
            </flux:text>
            <flux:text variant="strong" class="flex items-center gap-3" inline>
                <flux:icon.server variant="micro" class="fill-zinc-400 dark:fill-zinc-500" />
                {{ $site->repository_branch }}
            </flux:text>
        </div>
        <div class="flex flex-wrap gap-4 -my-1">
            <flux:button :href="route('servers.sites.files', [$server, $site])" wire:navigate>
                Edit files
            </flux:button>
            <flux:button :href="route('servers.sites.deployment-settings', [$server, $site])" wire:navigate>
                Edit deployment settings
            </flux:button>
            <flux:button :href="route('servers.sites.deployments', [$server, $site])" wire:navigate>
                View deployments
            </flux:button>
            <flux:button :href="route('servers.sites.deployments', [$server, $site])" variant="primary" color="zinc" wire:navigate>
                Deploy
            </flux:button>
        </div>
    </div>

    <flux:spacer class="mt-8" />

    <x-description.list>
        <x-description.term>
            <flux:text>Hostname</flux:text>
        </x-description.term>
        <x-description.details>
            <flux:text variant="strong">{{ $site->hostname }}</flux:text>
        </x-description.details>

        <x-description.term>
            <flux:text>PHP version</flux:text>
        </x-description.term>
        <x-description.details>
            <flux:text variant="strong">{{ $site->php_version }}</flux:text>
        </x-description.details>

        <x-description.term>
            <flux:text>Repository URL</flux:text>
        </x-description.term>
        <x-description.details>
            <flux:text variant="strong">{{ $site->repository_url }}</flux:text>
        </x-description.details>

        <x-description.term>
            <flux:text>Repository Branch</flux:text>
        </x-description.term>
        <x-description.details>
            <flux:text variant="strong">{{ $site->repository_branch }}</flux:text>
        </x-description.details>
    </x-description.list>
</div>
