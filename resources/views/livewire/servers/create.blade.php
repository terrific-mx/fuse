<?php

use App\Jobs\ProvisionServer;
use App\Models\Organization;
use App\Services\ServerNameGenerator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Volt\Component;

new #[Title('Add server')] class extends Component
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

<div class="mx-auto max-w-[512px]">
    <flux:link
        :href="route('servers.index')"
        class="inline-flex items-center gap-2 text-sm"
        variant="subtle"
        inline
        wire:navigate
    >
        <flux:icon.chevron-left variant="micro" />
        Servers
    </flux:link>

    <flux:spacer class="mt-4 lg:mt-8" />

    <form wire:submit="save">
        <flux:heading class="text-xl">Add a server</flux:heading>

        <flux:spacer class="mt-10" />

        <flux:callout variant="warning">
            <flux:callout.heading icon="exclamation-triangle">Important: Ubuntu 24.04 required</flux:callout.heading>
            <flux:callout.text>
                Please ensure your server is provisioned with Ubuntu version 24.04 for compatibility.
            </flux:callout.text>
        </flux:callout>

        <flux:spacer class="mt-8" />

        <div class="space-y-6">
            <flux:field>
                <flux:label>Name</flux:label>
                <flux:input.group>
                    <flux:input wire:model="name" required />
                    <flux:button type="button" icon="sparkles" wire:click="generateName">Generate</flux:button>
                </flux:input.group>
                <flux:error name="name" />
            </flux:field>

            <flux:input wire:model="ip_address" label="IP address" required />

            <flux:field>
                <flux:label>Memory</flux:label>
                <flux:input.group>
                    <flux:input wire:model="memory" type="number" min="512" max="1048576" required />
                    <flux:input.group.suffix>MB</flux:input.group.suffix>
                </flux:input.group>
                <flux:error name="memory" />
            </flux:field>

            <flux:pillbox wire:model="ssh_keys" label="SSH keys" multiple searchable>
                @foreach ($this->organization->sshKeys as $key)
                    <flux:pillbox.option value="{{ $key->id }}">{{ $key->name }}</flux:pillbox.option>
                @endforeach
            </flux:pillbox>
        </div>

        <flux:spacer class="mt-8" />

        <flux:button type="submit" variant="primary" color="zinc" class="w-full">Add server</flux:button>
    </form>
</div>
