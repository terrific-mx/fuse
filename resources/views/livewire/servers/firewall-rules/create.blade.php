<?php

use App\Models\Server;
use Livewire\Volt\Component;

new class extends Component
{
    public Server $server;

    public string $name = '';

    public string $action = '';

    public $port = '';

    public ?string $from_ip_address = null;

    public function mount()
    {
        $this->authorize('view', $this->server);
    }

    public function create()
    {
        $this->authorize('view', $this->server);

        $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'action' => ['required', 'in:allow,deny,reject'],
            'port' => ['required', 'integer', 'between:1,65535'],
            'from_ip_address' => ['nullable', 'ip'],
        ]);

        $rule = $this->server->firewallRules()->create([
            'name' => $this->name,
            'action' => $this->action,
            'port' => $this->port,
            'from_ip_address' => $this->from_ip_address,
            'status' => 'pending',
        ]);

        $rule->install();

        return redirect()->route('servers.firewall-rules.index', $this->server);
    }
}; ?>

<div>
    <flux:breadcrumbs>
        <flux:breadcrumbs.item :href="route('servers.index', $server)" wire:navigate>Servers</flux:breadcrumbs.item>
        <flux:breadcrumbs.item :href="route('servers.show', $server)" wire:navigate>
            {{ $server->name }}
        </flux:breadcrumbs.item>
        <flux:breadcrumbs.item :href="route('servers.firewall-rules.index', $server)" wire:navigate>
            Firewall Rules
        </flux:breadcrumbs.item>
    </flux:breadcrumbs>

    <flux:spacer class="mt-8" />

    <form wire:submit="create">
        <flux:heading size="xl">Create Firewall Rule</flux:heading>

        <flux:separator class="my-10 mt-6" />

        <section class="grid gap-x-8 gap-y-6 sm:grid-cols-2">
            <div>
                <flux:heading>Rule details</flux:heading>
            </div>
            <div class="space-y-6">
                <flux:field>
                    <flux:input wire:model="name" placeholder="Name" required autofocus />
                    <flux:error name="name" />
                </flux:field>
                <flux:field>
                    <flux:select wire:model.live="action" required placeholder="Select action...">
                        <flux:select.option value="allow">Allow</flux:select.option>
                        <flux:select.option value="deny">Deny</flux:select.option>
                        <flux:select.option value="reject">Reject</flux:select.option>
                    </flux:select>
                    <flux:error name="action" />
                </flux:field>
            </div>
        </section>

        <flux:separator variant="subtle" class="my-10" />

        <section class="grid gap-x-8 gap-y-6 sm:grid-cols-2">
            <div>
                <flux:heading>Source</flux:heading>
            </div>
            <div class="space-y-6">
                <flux:field>
                    <flux:input wire:model="port" placeholder="Port" required type="number" min="1" max="65535" />
                    <flux:error name="port" />
                </flux:field>
                <flux:field>
                    <flux:input wire:model="from_ip_address" placeholder="From IP Address (optional)" />
                    <flux:error name="from_ip_address" />
                </flux:field>
            </div>
        </section>

        <flux:separator variant="subtle" class="my-10" />

        <div class="flex justify-end gap-4">
            <flux:button :href="route('servers.firewall-rules.index', $server)" variant="ghost" wire:navigate>
                Cancel
            </flux:button>
            <flux:button type="submit" variant="primary" color="zinc">Create Firewall Rule</flux:button>
        </div>
    </form>
</div>
