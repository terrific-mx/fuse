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
            'status' => 'installing',
        ]);

        if (method_exists($rule, 'install')) {
            $rule->install();
        }

        return redirect()->route('servers.firewall-rules.index', $this->server);
    }
}; ?>

<div>
    <form wire:submit="create" class="mx-auto mt-12 max-w-lg space-y-6">
        <flux:heading size="lg">Create Firewall Rule</flux:heading>

        <div>
            <flux:input label="Name" name="name" wire:model="name" required autofocus />
        </div>

        <div>
            <flux:radio.group wire:model="action" label="Action" required>
                <flux:radio value="allow" label="Allow" />
                <flux:radio value="deny" label="Deny" />
                <flux:radio value="reject" label="Reject" />
            </flux:radio.group>
        </div>

        <div>
            <flux:input label="Port" name="port" wire:model="port" required type="number" min="1" max="65535" />
        </div>

        <div>
            <flux:input label="From IP Address" name="from_ip_address" wire:model="from_ip_address" placeholder="Optional" />
        </div>

        <div>
            <flux:button type="submit">Create</flux:button>
        </div>
    </form>
</div>
