<?php

use App\Models\FirewallRule;
use App\Models\Server;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Volt\Component;

new #[Title('Firewall rules')] class extends Component
{
    public Server $server;

    public function mount()
    {
        $this->authorize('view', $this->server);
    }

    #[Computed]
    public function firewallRules()
    {
        return $this->server->firewallRules()->paginate(10);
    }

    public function delete(FirewallRule $firewallRule)
    {
        $this->authorize('delete', $firewallRule);
        $firewallRule->uninstall();
    }
}; ?>

<x-layouts.server :server="$server">
    <header class="flex items-center">
        <flux:heading class="text-lg!">All firewall rules</flux:heading>
        <flux:spacer />
        <flux:button
            :href="route('servers.firewall-rules.create', $server)"
            variant="primary"
            color="zinc"
            size="sm"
            icon="plus"
            wire:navigate
        >
            New firewall rule
        </flux:button>
    </header>

    <flux:separator class="mt-6" />

    <flux:table :paginate="$this->firewallRules" wire:poll>
        <flux:table.rows>
            @foreach ($this->firewallRules as $firewallRule)
                <flux:table.row :key="$firewallRule->id">
                    <flux:table.cell variant="strong" class="w-full">
                        {{ $firewallRule->name }}
                    </flux:table.cell>
                    <flux:table.cell>
                        {{ ucfirst($firewallRule->action) }}
                    </flux:table.cell>
                    <flux:table.cell>
                        {{ $firewallRule->port }}
                    </flux:table.cell>
                    <flux:table.cell>
                        {{ $firewallRule->from_ip_address ?? 'Any' }}
                    </flux:table.cell>
                    <flux:table.cell>
                        {{ ucfirst($firewallRule->status) }}
                    </flux:table.cell>
                    <flux:table.cell align="end">
                        <flux:button
                            wire:confirm="Are you sure you want to delete this firewall rule?"
                            wire:click="delete({{ $firewallRule->id }})"
                            :disabled="$firewallRule->status !== 'installed'"
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
