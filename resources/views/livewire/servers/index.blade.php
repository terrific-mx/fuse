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

<div class="max-w-3xl mx-auto">
    <header class="flex items-center">
        <flux:heading class="text-xl">All servers</flux:heading>
        <flux:spacer />
        <flux:button :href="route('servers.create')" variant="primary" color="zinc" size="sm" icon="plus" wire:navigate>
            New server
        </flux:button>
    </header>

    <flux:separator class="mt-6" />

    <div>
        <flux:table :paginate="$this->servers" wire:poll>
            <flux:table.rows>
                @foreach ($this->servers as $server)
                    <flux:table.row :key="$server->id">
                        <flux:table.cell>
                            <div class="flex items-center gap-3">
                                <flux:avatar :name="strtoupper($server->name)" size="xs" color="auto" initials:single :color:seed="$server->id" />
                                <flux:link :href="route('servers.show', $server)" :accent="false">{{ $server->name }}</flux:link>
                                <flux:badge
                                    :color="$server->status_color"
                                    size="sm"
                                    inset="top bottom"
                                    @class(['animate-pulse' => $server->is_provisioning])
                                >
                                    {{ $server->status_formatted }}
                                </flux:badge>
                            </div>
                        </flux:table.cell>
                        <flux:table.cell align="end" class="text-xs">
                            <span>{{ $server->created_at->format('M d') }}</span>
                        </flux:table.cell>
                    </flux:table.row>
                @endforeach
            </flux:table.rows>
        </flux:table>
    </div>
</div>
