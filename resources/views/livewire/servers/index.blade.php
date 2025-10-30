<?php

use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Volt\Component;
use Livewire\WithPagination;

new #[Title('Servers')] class extends Component
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
    <header class="flex items-center">
        <flux:heading size="xl">Servers</flux:heading>
        <flux:spacer />
        <flux:button :href="route('servers.create')" variant="primary" color="zinc" wire:navigate>
            New server
        </flux:button>
    </header>

    <flux:spacer class="mt-8" />

    <div>
        <flux:table :paginate="$this->servers" wire:poll>
            <flux:table.columns>
                <flux:table.column>Name</flux:table.column>
                <flux:table.column>IP Address</flux:table.column>
                <flux:table.column align="end">Status</flux:table.column>
            </flux:table.columns>
            <flux:table.rows>
                @foreach ($this->servers as $server)
                    <flux:table.row :key="$server->id" class="hover:bg-zinc-950/2.5 dark:hover:bg-white/2.5">
                        <flux:table.cell variant="strong" class="relative">
                            <a href="{{ route('servers.show', $server) }}" class="absolute inset-0" wire:navigate></a>
                            {{ $server->name }}
                        </flux:table.cell>
                        <flux:table.cell class="relative">
                            <a href="{{ route('servers.show', $server) }}" class="absolute inset-0" wire:navigate></a>
                            {{ $server->ip_address }}
                        </flux:table.cell>
                        <flux:table.cell align="end" class="relative">
                            <a href="{{ route('servers.show', $server) }}" class="absolute inset-0" wire:navigate></a>
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
