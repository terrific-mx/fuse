<?php

use App\Models\FirewallRule;
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

<div wire:poll class="space-y-8">
    <header class="-mt-6 flex items-center lg:-mt-8">
        <flux:heading size="lg">{{ $server->name }}</flux:heading>
        <flux:spacer />
        @include('partials.server-navbar')
    </header>

    <section class="mt-12">
        <div class="flex items-center justify-between">
            <flux:heading size="xl">Firewall Rules</flux:heading>
            <flux:button
                :href="route('servers.firewall-rules.create', $server)"
                variant="primary"
                size="sm"
                color="zinc"
            >
                Add firewall rule
            </flux:button>
        </div>

        <div class="mt-4">
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
                            <flux:table.cell>{{ $firewallRule->name }}</flux:table.cell>
                            <flux:table.cell>{{ ucfirst($firewallRule->action) }}</flux:table.cell>
                            <flux:table.cell>{{ $firewallRule->port }}</flux:table.cell>
                            <flux:table.cell>{{ $firewallRule->from_ip_address ?? 'Any' }}</flux:table.cell>
                            <flux:table.cell>{{ ucfirst($firewallRule->status) }}</flux:table.cell>
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
    </section>
</div>
