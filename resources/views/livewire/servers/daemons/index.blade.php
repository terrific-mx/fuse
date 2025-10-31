<?php

use App\Models\Server;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Volt\Component;

new #[Title('Daemons')] class extends Component
{
    public Server $server;

    public function mount()
    {
        $this->authorize('view', $this->server);
    }

    #[Computed]
    public function daemons()
    {
        return $this->server->daemons()->paginate(10);
    }
}; ?>

<x-layouts.server :server="$server">
    <header class="flex items-center">
        <flux:heading class="text-lg!">All daemons</flux:heading>
        <flux:spacer />
        <flux:button
            :href="route('servers.daemons.create', $server)"
            variant="primary"
            color="zinc"
            size="sm"
            icon="plus"
            wire:navigate
        >
            New daemon
        </flux:button>
    </header>

    <flux:separator class="mt-6" />

    <flux:table :paginate="$this->daemons" wire:poll>
        <flux:table.rows>
            @foreach ($this->daemons as $daemon)
                <flux:table.row :key="$daemon->id">
                    <flux:table.cell variant="strong" class="w-full">
                        {{ $daemon->command }}
                    </flux:table.cell>
                    <flux:table.cell>
                        {{ $daemon->directory }}
                    </flux:table.cell>
                    <flux:table.cell>
                        {{ $daemon->user }}
                    </flux:table.cell>
                    <flux:table.cell>
                        {{ $daemon->processes }}
                    </flux:table.cell>
                    <flux:table.cell>
                        {{ $daemon->stop_wait_seconds }}
                    </flux:table.cell>
                    <flux:table.cell>
                        {{ $daemon->stop_signal }}
                    </flux:table.cell>
                </flux:table.row>
            @endforeach
        </flux:table.rows>
    </flux:table>
</x-layouts.server>
