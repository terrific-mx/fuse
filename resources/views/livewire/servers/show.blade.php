<?php

use App\Models\Server;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Volt\Component;

new #[Title('Server details')] class extends Component
{
    public Server $server;

    public function mount()
    {
        $this->authorize('view', $this->server);
    }

    #[Computed]
    public function sites()
    {
        return $this->server->sites()->latest()->limit(3)->get();
    }
}; ?>

<x-layouts.server :server="$server">
    <header class="flex items-center">
        <flux:heading class="text-lg!">Recent sites</flux:heading>
        <flux:spacer />
        <flux:button
            :href="route('servers.sites.create', $server)"
            variant="primary"
            color="zinc"
            size="sm"
            icon="plus"
            wire:navigate
        >
            New site
        </flux:button>
    </header>

    <flux:separator class="mt-6" />

    @if ($this->sites->count())
        <flux:table>
            <flux:table.rows>
                @foreach ($this->sites as $site)
                    <flux:table.row :key="$site->id">
                        <flux:table.cell class="w-full">
                            <flux:link
                                href="{{ route('servers.sites.show', ['server' => $server, 'site' => $site]) }}"
                                :accent="false"
                                wire:navigate
                            >
                                {{ $site->hostname }}
                            </flux:link>
                        </flux:table.cell>
                        <flux:table.cell align="end">
                            {{ $site->repository_url }}
                        </flux:table.cell>
                    </flux:table.row>
                @endforeach
            </flux:table.rows>
        </flux:table>
    @else
        <flux:callout variant="secondary">No sites have been created for this server yet.</flux:callout>
    @endif
</x-layouts.server>
