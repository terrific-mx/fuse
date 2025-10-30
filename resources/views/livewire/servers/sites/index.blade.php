<?php

use App\Models\Server;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Volt\Component;

new #[Title('Sites')] class extends Component
{
    public Server $server;

    public function mount()
    {
        $this->authorize('view', $this->server);
    }

    #[Computed]
    public function sites()
    {
        return $this->server->sites()->orderByDesc('created_at')->get();
    }
}; ?>

<div>
    <flux:breadcrumbs>
        <flux:breadcrumbs.item :href="route('servers.index')" wire:navigate>Servers</flux:breadcrumbs.item>
        <flux:breadcrumbs.item :href="route('servers.show', $server)" wire:navigate>
            {{ $server->name }}
        </flux:breadcrumbs.item>
    </flux:breadcrumbs>

    <flux:spacer class="mt-8" />

    <header class="flex items-center">
        <flux:heading size="xl">Sites</flux:heading>
        <flux:spacer />
         <flux:button :href="route('servers.sites.create', $server)" variant="primary" color="zinc" class="-my-1" wire:navigate>New site</flux:button>
    </header>

    <flux:spacer class="mt-8" />

    <div>
        <flux:table>
            <flux:table.columns>
                <flux:table.column>Hostname</flux:table.column>
                <flux:table.column>Repository</flux:table.column>
            </flux:table.columns>
            <flux:table.rows>
                @foreach ($this->sites as $site)
                    <flux:table.row :key="$site->id" class="hover:bg-zinc-950/2.5 dark:hover:bg-white/2.5">
                        <flux:table.cell variant="strong" class="relative">
                            <a
                                href="{{ route('servers.sites.show', ['server' => $server, 'site' => $site]) }}"
                                class="absolute inset-0"
                                wire:navigate
                            ></a>
                            {{ $site->hostname }}
                        </flux:table.cell>
                        <flux:table.cell class="relative">
                            <a
                                href="{{ route('servers.sites.show', ['server' => $server, 'site' => $site]) }}"
                                class="absolute inset-0"
                                wire:navigate
                            ></a>
                            {{ $site->repository_url }}
                        </flux:table.cell>
                    </flux:table.row>
                @endforeach
            </flux:table.rows>
        </flux:table>
    </div>

</div>
