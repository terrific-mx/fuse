<?php

use App\Models\Server;
use Livewire\Attributes\Title;
use Livewire\Volt\Component;

new #[Title('Create firewall rule')] class extends Component
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

<div class="max-w-[512px] mx-auto">
    <flux:link :href="route('servers.firewall-rules.index', $server)" class="inline-flex items-center gap-2 text-sm" variant="subtle" inline wire:navigate>
        <flux:icon.chevron-left variant="micro" />
        Firewall rules
    </flux:link>

    <flux:spacer class="mt-4 lg:mt-8" />

    <form wire:submit="create">
        <flux:heading class="text-xl">Create firewall rule</flux:heading>

        <flux:spacer class="mt-10" />

        <div class="space-y-6">
            <flux:field>
                <flux:input wire:model="name" label="Name" required autofocus />
                <flux:error name="name" />
            </flux:field>
            <flux:field>
                <flux:select wire:model.live="action" label="Action" required placeholder="Select action...">
                    <flux:select.option value="allow">Allow</flux:select.option>
                    <flux:select.option value="deny">Deny</flux:select.option>
                    <flux:select.option value="reject">Reject</flux:select.option>
                </flux:select>
                <flux:error name="action" />
            </flux:field>
            <flux:field>
                <flux:input wire:model="port" label="Port" required type="number" min="1" max="65535" />
                <flux:error name="port" />
            </flux:field>
            <flux:field>
                <flux:input wire:model="from_ip_address" label="From IP address (optional)" />
                <flux:error name="from_ip_address" />
            </flux:field>
        </div>

        <flux:spacer class="mt-8" />

        <div class="flex flex-col gap-4">
            <flux:button type="submit" variant="primary" color="zinc" class="w-full">Create firewall rule</flux:button>
        </div>
    </form>
</div>

