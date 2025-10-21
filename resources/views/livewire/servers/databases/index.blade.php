<?php

use App\Models\Database;
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
    public function databases()
    {
        return $this->server->databases()->paginate(10);
    }

    public function delete(Database $database)
    {
        $this->authorize('delete', $database);

        $database->purge();
    }
}; ?>

<div wire:poll class="space-y-8">
    <header class="-mt-6 flex items-center lg:-mt-8">
        <flux:heading size="lg">{{ $server->name }}</flux:heading>
        <flux:spacer />
        @include('partials.server-navbar')
    </header>

    <section class="mt-12">
        <div class="flex items-center justify-between">
            <flux:heading size="xl">Databases</flux:heading>
            <flux:button :href="route('servers.databases.create', $server)" variant="primary" size="sm" color="zinc">
                Add database
            </flux:button>
        </div>

        <div class="mt-4">
            <flux:table :paginate="$this->databases">
                <flux:table.columns>
                    <flux:table.column>Name</flux:table.column>
                    <flux:table.column>Status</flux:table.column>
                    <flux:table.column></flux:table.column>
                </flux:table.columns>
                <flux:table.rows>
                    @foreach ($this->databases as $database)
                        <flux:table.row :key="$database->id">
                            <flux:table.cell>{{ $database->name }}</flux:table.cell>
                            <flux:table.cell>{{ ucfirst($database->status) }}</flux:table.cell>
                            <flux:table.cell align="end">
                                <flux:button
                                    wire:confirm="Are you sure you want to delete this database?"
                                    wire:click="delete({{ $database->id }})"
                                    :disabled="$database->isDeleting()"
                                    inset="top bottom"
                                    variant="subtle"
                                    size="sm"
                                >
                                    Delete
                                </flux:button>
                            </flux:table.cell>
                        </flux:table.row>
                    @endforeach
                </flux:table.rows>
            </flux:table>
        </div>
    </section>
</div>
