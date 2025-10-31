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

<x-layouts.server :server="$server">
    <header class="flex items-center">
        <flux:heading size="lg">All databases</flux:heading>
        <flux:spacer />
        <flux:button
            :href="route('servers.databases.create', $server)"
            variant="primary"
            color="zinc"
            size="sm"
            icon="plus"
            class="-my-1"
            wire:navigate
        >
            New database
        </flux:button>
    </header>

    <flux:separator class="mt-3" />

    <flux:table :paginate="$this->databases">
        <flux:table.rows>
            @foreach ($this->databases as $database)
                <flux:table.row :key="$database->id">
                    <flux:table.cell variant="strong" class="w-full">{{ $database->name }}</flux:table.cell>
                    <flux:table.cell align="end">{{ ucfirst($database->status) }}</flux:table.cell>
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
</x-layouts.server>
