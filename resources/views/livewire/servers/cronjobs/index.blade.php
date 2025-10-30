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

<div wire:poll>
    <flux:breadcrumbs>
        <flux:breadcrumbs.item :href="route('servers.index')" wire:navigate>Servers</flux:breadcrumbs.item>
        <flux:breadcrumbs.item :href="route('servers.show', $server)" wire:navigate>
            {{ $server->name }}
        </flux:breadcrumbs.item>
    </flux:breadcrumbs>

    <flux:spacer class="mt-8" />

    <header class="flex items-center">
        <flux:heading size="xl">Cronjobs</flux:heading>
        <flux:spacer />
        <flux:button :href="route('servers.cronjobs.create', $server)" variant="primary" color="zinc" wire:navigate>
            Add cronjob
        </flux:button>
    </header>

    <flux:spacer class="mt-8" />

    <div>
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
                    <flux:table.row :key="$cronjob->id" class="hover:bg-zinc-950/2.5 dark:hover:bg-white/2.5">
                        <flux:table.cell class="relative">
                            <a
                                href="{{ route('servers.cronjobs.edit', [$server, $cronjob]) }}"
                                class="absolute inset-0"
                                wire:navigate
                            ></a>
                            {{ $cronjob->command }}
                        </flux:table.cell>
                        <flux:table.cell class="relative">
                            <a
                                href="{{ route('servers.cronjobs.edit', [$server, $cronjob]) }}"
                                class="absolute inset-0"
                                wire:navigate
                            ></a>
                            {{ $cronjob->frequency === 'custom' ? $cronjob->custom_expression : str_replace('_', ' ', ucfirst($cronjob->frequency)) }}
                        </flux:table.cell>
                        <flux:table.cell class="relative">
                            <a
                                href="{{ route('servers.cronjobs.edit', [$server, $cronjob]) }}"
                                class="absolute inset-0"
                                wire:navigate
                            ></a>
                            {{ $cronjob->user }}
                        </flux:table.cell>
                        <flux:table.cell class="relative">
                            <a
                                href="{{ route('servers.cronjobs.edit', [$server, $cronjob]) }}"
                                class="absolute inset-0"
                                wire:navigate
                            ></a>
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
    </div>
</div>
