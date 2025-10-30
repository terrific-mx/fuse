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

<div wire:poll>
    <flux:breadcrumbs>
        <flux:breadcrumbs.item :href="route('servers.index')" wire:navigate>Servers</flux:breadcrumbs.item>
        <flux:breadcrumbs.item :href="route('servers.show', $server)" wire:navigate>
            {{ $server->name }}
        </flux:breadcrumbs.item>
    </flux:breadcrumbs>

    <flux:spacer class="mt-8" />

    <header class="flex items-center">
        <flux:heading size="xl">Firewall Rules</flux:heading>
        <flux:spacer />
        <flux:button
            :href="route('servers.firewall-rules.create', $server)"
            variant="primary"
            color="zinc"
            wire:navigate
        >
            Add firewall rule
        </flux:button>
    </header>

    <flux:spacer class="mt-8" />

    <div>
        <flux:table :paginate="$this->firewallRules">
            <flux:table.columns>
                <flux:table.column>Name</flux:table.column>
                <flux:table.column>Action</flux:table.column>
                <flux:table.column>Port</flux:table.column>
                <flux:table.column>From IP</flux:table.column>
                <flux:table.column>Status</flux:table.column>
                <flux:table.column></flux:table.column>
            </flux:table.columns>
            <flux:table.rows>
                @foreach ($this->firewallRules as $firewallRule)
                    <flux:table.row :key="$firewallRule->id">
                        <flux:table.cell variant="strong">
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
    </div>
</div>
