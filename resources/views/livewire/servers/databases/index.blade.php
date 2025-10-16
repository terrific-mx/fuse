<?php

use App\Models\Server;
use Livewire\Volt\Component;

new class extends Component
{
    public Server $server;

    public function mount()
    {
        $this->authorize('view', $this->server);
    }

    #[\Livewire\Attributes\Computed]
    public function databases()
    {
        return $this->server->databases()->paginate(10);
    }
}; ?>

<div wire:poll class="space-y-8">
    <header class="mb-6 space-y-2">
        <flux:heading size="xl">{{ $server->name }}</flux:heading>
        @include('partials.server-navbar')
    </header>

    <div class="mb-4">
        <flux:button :href="route('servers.databases.create', $server)">Create database</flux:button>
    </div>

    <div>
        <flux:table :paginate="$this->databases">
            <flux:table.columns>
                <flux:table.column>Name</flux:table.column>
                <flux:table.column>Status</flux:table.column>
            </flux:table.columns>
            <flux:table.rows>
                @foreach ($this->databases as $database)
                    <flux:table.row :key="$database->id">
                        <flux:table.cell>{{ $database->name }}</flux:table.cell>
                        <flux:table.cell>{{ ucfirst($database->status) }}</flux:table.cell>
                    </flux:table.row>
                @endforeach
            </flux:table.rows>
        </flux:table>
    </div>
</div>
