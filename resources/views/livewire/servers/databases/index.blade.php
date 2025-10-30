<?php

use App\Models\Database;
use App\Models\Server;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Volt\Component;

new #[Title('Databases')] class extends Component
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

        $database->uninstall();
    }
}; ?>

<div wire:poll>
    <flux:breadcrumbs>
        <flux:breadcrumbs.item :href="route('servers.index')" wire:navigate>Servers</flux:breadcrumbs.item>
        <flux:breadcrumbs.item :href="route('servers.show', $server)" wire:navigate>
            {{ $server->name }}
        </flux:breadcrumbs.item>
    </flux:breadcrumbs>

    <flux:spacer class="mt-8" />

    <header class="flex items-center">
        <flux:heading size="xl">Databases</flux:heading>
        <flux:spacer />
        <flux:button :href="route('servers.databases.create', $server)" variant="primary" color="zinc" wire:navigate>
            Add database
        </flux:button>
    </header>

    <flux:spacer class="mt-8" />

    <div>
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
                                :disabled="!$database->isInstalled()"
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
</div>
