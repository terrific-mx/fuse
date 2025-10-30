<?php

use App\Models\Server;
use Livewire\Attributes\Computed;
use Livewire\Volt\Component;

new class extends Component
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

<div wire:poll>
    <flux:breadcrumbs>
        <flux:breadcrumbs.item :href="route('servers.index')" wire:navigate>
            Servers
        </flux:breadcrumbs.item>
        <flux:breadcrumbs.item :href="route('servers.show', $server)" wire:navigate>
            {{ $server->name }}
        </flux:breadcrumbs.item>
    </flux:breadcrumbs>

    <flux:spacer class="mt-8" />

    <header class="flex items-center">
        <flux:heading size="xl">Daemons</flux:heading>
        <flux:spacer />
        <flux:button :href="route('servers.daemons.create', $server)" variant="primary" color="zinc" wire:navigate>
            Add daemon
        </flux:button>
    </header>

    <flux:spacer class="mt-8" />

    <div>
        <flux:table :paginate="$this->daemons">
            <flux:table.columns>
                <flux:table.column>Command</flux:table.column>
                <flux:table.column>Directory</flux:table.column>
                <flux:table.column>User</flux:table.column>
                <flux:table.column>Processes</flux:table.column>
                <flux:table.column>Stop Wait</flux:table.column>
                <flux:table.column>Stop Signal</flux:table.column>
                <flux:table.column>Status</flux:table.column>
            </flux:table.columns>
            <flux:table.rows>
                @foreach ($this->daemons as $daemon)
                    <flux:table.row :key="$daemon->id">
                        <flux:table.cell variant="strong">{{ $daemon->command }}</flux:table.cell>
                        <flux:table.cell>{{ $daemon->directory }}</flux:table.cell>
                        <flux:table.cell>{{ $daemon->user }}</flux:table.cell>
                        <flux:table.cell>{{ $daemon->processes }}</flux:table.cell>
                        <flux:table.cell>{{ $daemon->stop_wait_seconds }}</flux:table.cell>
                        <flux:table.cell>{{ $daemon->stop_signal }}</flux:table.cell>
                        <flux:table.cell>{{ ucfirst($daemon->status) }}</flux:table.cell>
                    </flux:table.row>
                @endforeach
            </flux:table.rows>
        </flux:table>
    </div>
</div>
