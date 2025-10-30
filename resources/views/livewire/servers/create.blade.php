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
    <flux:breadcrumbs>
        <flux:breadcrumbs.item :href="route('servers.index')" wire:navigate>Servers</flux:breadcrumbs.item>
    </flux:breadcrumbs>

    <flux:spacer class="mt-8" />

    <form wire:submit="save">
        <flux:heading size="xl">Add server</flux:heading>

        <flux:separator class="my-10 mt-6" />

        <flux:callout variant="warning">
            <flux:callout.heading icon="exclamation-triangle">Important: Ubuntu 24.04 required</flux:callout.heading>
            <flux:callout.text>
                Please ensure your server is provisioned with Ubuntu version 24.04 for compatibility.
            </flux:callout.text>
        </flux:callout>

        <flux:separator variant="subtle" class="my-10" />

        <section class="grid gap-x-8 gap-y-6 sm:grid-cols-2">
            <div>
                <flux:heading>Name</flux:heading>
            </div>
            <div>
                <flux:input.group>
                    <flux:input wire:model="name" required />
                    <flux:button type="button" icon="sparkles" wire:click="generateName">Generate</flux:button>
                </flux:input.group>
                <flux:error name="name" />
            </div>
        </section>

        <flux:separator variant="subtle" class="my-10" />

        <section class="grid gap-x-8 gap-y-6 sm:grid-cols-2">
            <div>
                <flux:heading>Server details</flux:heading>
            </div>
            <div class="space-y-6">
                <flux:field>
                    <flux:input wire:model="ip_address" required placeholder="IP address" />
                    <flux:error name="ip_address" />
                </flux:field>
                <flux:field>
                    <flux:input.group>
                        <flux:input
                            wire:model="memory"
                            type="number"
                            min="512"
                            max="1048576"
                            required
                            placeholder="Memory"
                        />
                        <flux:input.group.suffix>MB</flux:input.group.suffix>
                    </flux:input.group>
                    <flux:error name="memory" />
                </flux:field>
            </div>
        </section>

        <flux:separator variant="subtle" class="my-10" />

        <section class="grid gap-x-8 gap-y-6 sm:grid-cols-2">
            <div>
                <flux:heading>SSH keys</flux:heading>
            </div>
            <div>
                <flux:pillbox wire:model="ssh_keys" multiple searchable>
                    @foreach ($this->organization->sshKeys as $key)
                        <flux:pillbox.option value="{{ $key->id }}">{{ $key->name }}</flux:pillbox.option>
                    @endforeach
                </flux:pillbox>
                <flux:error name="ssh_keys" />
            </div>
        </section>

        <flux:separator variant="subtle" class="my-10" />

        <div class="flex justify-end gap-4">
            <flux:button :href="route('servers.index')" variant="ghost" wire:navigate>Cancel</flux:button>
            <flux:button type="submit" variant="primary" color="zinc">Add server</flux:button>
        </div>
    </form>
</div>
