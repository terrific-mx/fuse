<?php

use App\Jobs\ProvisionServer;
use App\Models\Organization;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Computed;
use Livewire\Volt\Component;

new class extends Component
{
    public string $name = '';

    public string $ip_address = '';

    public array $ssh_keys = [];

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
    <flux:spacer class="mt-3" />
    <flux:heading size="xl">Add server</flux:heading>

    <form wire:submit="save" class="space-y-6 mt-6">
        <flux:input
            label="Name"
            wire:model="name"
            required
        />

        <flux:input
            label="IP address"
            wire:model="ip_address"
            required
        />

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

        <flux:callout variant="warning">
            <flux:callout.heading icon="exclamation-triangle">Important: Ubuntu 24.04 required</flux:callout.heading>
            <flux:callout.text>
                Please ensure your server is provisioned with Ubuntu version 24.04 for compatibility.
            </flux:callout.text>
        </flux:callout>

        <flux:callout variant="secondary">
            <flux:callout.heading icon="information-circle">Organization SSH key required</flux:callout.heading>
            <flux:callout.text>
                To provision your server, ensure the organization SSH public key is installed on the server you create with your cloud provider.
            </flux:callout.text>
            <x-slot name="actions">
                <flux:input icon="key" value="{{ $this->organization->ssh_public_key }}" readonly copyable />
            </x-slot>
        </flux:callout>

        <div class="flex gap-4">
            <flux:button type="submit" variant="primary">Add server</flux:button>
            <flux:button :href="route('servers.index')" variant="ghost" wire:navigate>Cancel</flux:button>
        </div>
    </form>
</div>
