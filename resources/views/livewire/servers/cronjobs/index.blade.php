<?php

use App\Models\Cronjob;
use App\Models\Server;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Volt\Component;

new #[Title('Cronjobs')] class extends Component
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

<x-layouts.server :server="$server">
    <header class="flex items-center">
        <flux:heading class="text-lg!">All cronjobs</flux:heading>
        <flux:spacer />
        <flux:button :href="route('servers.cronjobs.create', $server)" variant="primary" color="zinc" size="sm" icon="plus" wire:navigate>
            New cronjob
        </flux:button>
    </header>

    <flux:separator class="mt-6" />

    <flux:table :paginate="$this->cronjobs" wire:poll>
        <flux:table.rows>
            @foreach ($this->cronjobs as $cronjob)
                <flux:table.row :key="$cronjob->id">
                    <flux:table.cell class="w-full">
                        <flux:link
                            href="{{ route('servers.cronjobs.edit', [$server, $cronjob]) }}"
                            :accent="false"
                            wire:navigate
                        >{{ $cronjob->command }}</flux:link>
                    </flux:table.cell>
                    <flux:table.cell>
                        {{ $cronjob->frequency === 'custom' ? $cronjob->custom_expression : str_replace('_', ' ', ucfirst($cronjob->frequency)) }}
                    </flux:table.cell>
                    <flux:table.cell>
                        {{ $cronjob->user }}
                    </flux:table.cell>
                    <flux:table.cell>
                        {{ ucfirst($cronjob->status) }}
                    </flux:table.cell>
                    <flux:table.cell align="end">
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
</x-layouts.server>
