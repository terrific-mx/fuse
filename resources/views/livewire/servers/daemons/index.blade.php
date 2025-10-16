<?php

use App\Models\Server;
use Livewire\Attributes\Computed;
use Livewire\Volt\Component;

new class extends Component
{
    public Server $server;

    #[Computed]
    public function daemons()
    {
        return $this->server->daemons()->paginate(10);
    }
}; ?>

<div wire:poll class="space-y-8">
    <header class="mb-6 space-y-2">
        <flux:heading size="xl">{{ $server->name }}</flux:heading>
        @include('partials.server-navbar')
    </header>

    <div class="mb-4">
        <flux:button :href="route('servers.daemons.create', $server)">Add daemon</flux:button>
    </div>

    <div>
        <flux:table :paginate="$this->daemons">
            <flux:table.columns>
                <flux:table.column>Command</flux:table.column>
                <flux:table.column>Directory</flux:table.column>
                <flux:table.column>User</flux:table.column>
                <flux:table.column>Processes</flux:table.column>
                <flux:table.column>Stop Wait</flux:table.column>
                <flux:table.column>Stop Signal</flux:table.column>
                <flux:table.column>Installed At</flux:table.column>
                <flux:table.column>Failed At</flux:table.column>
            </flux:table.columns>
            <flux:table.rows>
                @foreach ($this->daemons as $daemon)
                    <flux:table.row :key="$daemon->id">
                        <flux:table.cell>{{ $daemon->command }}</flux:table.cell>
                        <flux:table.cell>{{ $daemon->directory }}</flux:table.cell>
                        <flux:table.cell>{{ $daemon->user }}</flux:table.cell>
                        <flux:table.cell>{{ $daemon->processes }}</flux:table.cell>
                        <flux:table.cell>{{ $daemon->stop_wait_seconds }}</flux:table.cell>
                        <flux:table.cell>{{ $daemon->stop_signal }}</flux:table.cell>
                        <flux:table.cell>{{ $daemon->installed_at?->format('Y-m-d H:i') }}</flux:table.cell>
                        <flux:table.cell>{{ $daemon->failed_at?->format('Y-m-d H:i') }}</flux:table.cell>
                    </flux:table.row>
                @endforeach
            </flux:table.rows>
        </flux:table>
    </div>
</div>
