<?php

use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Volt\Component;
use Livewire\WithPagination;

new class extends Component
{
    use WithPagination;

    #[Computed]
    public function organization()
    {
        return Auth::user()->currentOrganization;
    }

    #[Computed]
    public function servers()
    {
        return $this->organization->servers()
            ->orderByDesc('created_at')
            ->paginate(10);
    }
}; ?>

<div>
    <header class="flex items-center -mt-6 lg:-mt-8">
        <flux:heading size="lg">Servers</flux:heading>
        <flux:spacer />
        <div class="flex items-center gap-4">
            <flux:navbar>
                <flux:navbar.item :href="route('servers.index')" :accent="false" wire:navigate>
                    Overview
                </flux:navbar.item>
            </flux:navbar>

            <flux:button :href="route('servers.create')" variant="primary" color="zinc" size="sm" wire:navigate>
                Add
            </flux:button>
        </div>
    </header>

    <flux:spacer class="mt-12" />

    <flux:heading size="xl">Servers</flux:heading>

    <div class="mt-6">
        <flux:table :paginate="$this->servers" wire:poll>
            <flux:table.columns>
                <flux:table.column>Name</flux:table.column>
                <flux:table.column>IP Address</flux:table.column>
                <flux:table.column align="end">Status</flux:table.column>
            </flux:table.columns>
            <flux:table.rows>
                @foreach ($this->servers as $server)
                    <flux:table.row :key="$server->id">
                        <flux:table.cell>
                            <flux:link :href="route('servers.show', $server)" wire:navigate>
                                {{ $server->name }}
                            </flux:link>
                        </flux:table.cell>
                        <flux:table.cell>{{ $server->ip_address }}</flux:table.cell>
                        <flux:table.cell align="end">
                            <flux:badge
                                :color="$server->status_color"
                                size="sm"
                                inset="top bottom"
                                @class(['animate-pulse' => $server->is_provisioning])
                            >
                                {{ $server->status_formatted }}
                            </flux:badge>
                        </flux:table.cell>
                    </flux:table.row>
                @endforeach
            </flux:table.rows>
        </flux:table>
    </div>


</div>
