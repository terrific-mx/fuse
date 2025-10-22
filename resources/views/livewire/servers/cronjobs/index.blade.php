<?php

use App\Models\Cronjob;
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
    public function cronjobs()
    {
        return $this->server->cronjobs()->paginate(10);
    }

    public function delete(Cronjob $cronjob)
    {
        $this->authorize('delete', $cronjob);
        $cronjob->uninstall();
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
            <flux:heading size="xl">Cronjobs</flux:heading>
            <flux:button :href="route('servers.cronjobs.create', $server)" variant="primary" size="sm" color="zinc">
                Add cronjob
            </flux:button>
        </div>

        <div class="mt-4">
            <flux:table :paginate="$this->cronjobs">
                <flux:table.columns>
                    <flux:table.column>Command</flux:table.column>
                    <flux:table.column>Frequency</flux:table.column>
                    <flux:table.column>User</flux:table.column>
                    <flux:table.column>Status</flux:table.column>
                    <flux:table.column></flux:table.column>
                </flux:table.columns>
                <flux:table.rows>
                    @foreach ($this->cronjobs as $cronjob)
                        <flux:table.row :key="$cronjob->id">
                            <flux:table.cell>{{ $cronjob->command }}</flux:table.cell>
                            <flux:table.cell>
                                {{ $cronjob->frequency === 'custom' ? $cronjob->custom_expression : str_replace('_', ' ', ucfirst($cronjob->frequency)) }}
                            </flux:table.cell>
                            <flux:table.cell>{{ $cronjob->user }}</flux:table.cell>
                            <flux:table.cell>{{ ucfirst($cronjob->status) }}</flux:table.cell>
                            <flux:table.cell align="end">
                                <flux:button
                                    :href="route('servers.cronjobs.edit', [$server, $cronjob])"
                                    variant="subtle"
                                    size="sm"
                                    class="mr-2"
                                >
                                    Edit
                                </flux:button>
                                <flux:button
                                    wire:confirm="Are you sure you want to delete this cronjob?"
                                    wire:click="delete({{ $cronjob->id }})"
                                    :disabled="!$cronjob->isInstalled()"
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
