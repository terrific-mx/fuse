<?php

use App\Models\Server;
use Livewire\Attributes\Computed;
use Livewire\Volt\Component;

new class extends Component
{
    public Server $server;

    #[Computed]
    public function recentSites()
    {
        return $this->server->sites()->orderByDesc('created_at')->limit(3)->get();
    }

    public function mount()
    {
        $this->authorize('view', $this->server);
    }
}; ?>

<div>

    <flux:breadcrumbs>
        <flux:breadcrumbs.item :href="route('servers.index')" wire:navigate>Servers</flux:breadcrumbs.item>
    </flux:breadcrumbs>

    <flux:spacer class="mt-8" />

    <flux:heading size="xl">{{ $server->name }}</flux:heading>

    <div class="isolate mt-2.5 flex flex-wrap justify-between gap-x-6 gap-y-4 items-end">
        <div class="flex flex-wrap gap-x-10 gap-y-4">
            <flux:text variant="strong" class="flex items-center gap-3" inline>
                <flux:icon.server variant="micro" class="fill-zinc-400 dark:fill-zinc-500" />
                {{ $server->ip_address }}
            </flux:text>
        </div>
        <div class="flex flex-wrap gap-4">
            <flux:button.group>
                <flux:button :href="route('servers.sites.index', $server)" wire:navigate>View sites</flux:button>
                <flux:dropdown align="end">
                    <flux:button icon="chevron-down"></flux:button>
                    <flux:menu>
                        <flux:menu.item :href="route('servers.databases.index', $server)" wire:navigate>View databases</flux:menu.item>
                        <flux:menu.separator />
                        <flux:menu.item :href="route('servers.cronjobs.index', $server)" wire:navigate>View cronjobs</flux:menu.item>
                        <flux:menu.separator />
                        <flux:menu.item :href="route('servers.firewall-rules.index', $server)" wire:navigate>View firewall rules</flux:menu.item>
                        <flux:menu.separator />
                        <flux:menu.item :href="route('servers.daemons.index', $server)" wire:navigate>View daemons</flux:menu.item>
                    </flux:menu>
                </flux:dropdown>
            </flux:button.group>
        </div>
    </div>

    <flux:spacer class="mt-8" />

    <div class="flex justify-between gap-4 items-end">
        <flux:heading>
            Recent Sites
        </flux:heading>
        <flux:button :href="route('servers.sites.index', $server)" wire:navigate>View all</flux:button>
    </div>

    <flux:spacer class="mt-4" />

    @if ($this->recentSites->count())
        <flux:table>
            <flux:table.columns>
                <flux:table.column>Hostname</flux:table.column>
                <flux:table.column>Repository</flux:table.column>
            </flux:table.columns>
            <flux:table.rows>
                @foreach ($this->recentSites as $site)
                    <flux:table.row :key="$site->id" class="hover:bg-zinc-950/2.5 dark:hover:bg-white/2.5">
                        <flux:table.cell variant="strong" class="relative">
                            <a href="{{ route('servers.sites.show', ['server' => $server, 'site' => $site]) }}" class="absolute inset-0" wire:navigate></a>
                            {{ $site->hostname }}
                        </flux:table.cell>
                        <flux:table.cell class="relative">
                            <a href="{{ route('servers.sites.show', ['server' => $server, 'site' => $site]) }}" class="absolute inset-0" wire:navigate></a>
                            {{ $site->repository_url }}
                        </flux:table.cell>
                    </flux:table.row>
                @endforeach
            </flux:table.rows>
        </flux:table>
    @else
        <flux:callout variant="secondary" class="mt-2">No sites have been created for this server yet.</flux:callout>
    @endif
</div>

