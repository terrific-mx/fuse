<?php

use App\Jobs\ProvisionServer;
use App\Models\Organization;
use App\Services\ServerNameGenerator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Computed;
use Livewire\Volt\Component;

new class extends Component
{
    public string $name = '';

    public string $ip_address = '';

    public ?int $memory = null;

    public array $ssh_keys = [];

    public function generateName(): void
    {
        $this->name = ServerNameGenerator::generate();
    }

    #[Computed]
    public function organization(): Organization
    {
        return Auth::user()->currentOrganization;
    }

    protected function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'ip_address' => ['required', 'ipv4'],
            'memory' => ['required', 'integer', 'min:512', 'max:1048576'],
            'ssh_keys' => ['array'],
            'ssh_keys.*' => [Rule::exists('ssh_keys', 'id')->where(fn ($q) => $q->where('organization_id', $this->organization->id))],
        ];
    }

    public function save(): void
    {
        $this->validate();

        $server = $this->organization->servers()->create([
            'created_by' => Auth::id(),
            'name' => $this->name,
            'ip_address' => $this->ip_address,
            'memory' => $this->memory,
            'database_password' => Str::random(40),
            'sudo_password' => Str::random(40),
        ]);

        if (! empty($this->ssh_keys)) {
            $server->sshKeys()->sync($this->ssh_keys);
        }

        ProvisionServer::dispatch($server);

        $this->redirectRoute('servers.provision-instructions', ['server' => $server], navigate: true);
    }
}; ?>

<div>
    <header class="-mt-6 flex items-center lg:-mt-8">
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

    <flux:heading size="xl">Add server</flux:heading>

    <form wire:submit="save" class="space-y-6 mt-6">
        <flux:callout variant="warning">
            <flux:callout.heading icon="exclamation-triangle">Important: Ubuntu 24.04 required</flux:callout.heading>
            <flux:callout.text>
                Please ensure your server is provisioned with Ubuntu version 24.04 for compatibility.
            </flux:callout.text>
        </flux:callout>

        <flux:field>
            <flux:label>Name</flux:label>
            <flux:input.group>
                <flux:input wire:model="name" required />
                <flux:button type="button" icon="sparkles" wire:click="generateName" class="ml-2">Generate</flux:button>
            </flux:input.group>
            <flux:error name="name" />
        </flux:field>

        <flux:input
            label="IP address"
            wire:model="ip_address"
            required
        />

        <flux:field>
            <flux:label>Memory</flux:label>
            <flux:input.group>
                <flux:input
                    wire:model="memory"
                    type="number"
                    min="512"
                    max="1048576"
                    required
                />
                <flux:input.group.suffix>MB</flux:input.group.suffix>
            </flux:input.group>
            <flux:error name="memory" />
        </flux:field>

        <flux:pillbox
            wire:model="ssh_keys"
            multiple
            searchable
            label="SSH keys"
        >
            @foreach ($this->organization->sshKeys as $key)
                <flux:pillbox.option value="{{ $key->id }}">{{ $key->name }}</flux:pillbox.option>
            @endforeach
        </flux:pillbox>

        <div class="flex gap-4">
            <flux:button type="submit" variant="primary">Add server</flux:button>
            <flux:button :href="route('servers.index')" variant="ghost" wire:navigate>Cancel</flux:button>
        </div>
    </form>
</div>
