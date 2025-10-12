<?php

use App\Livewire\Forms\ServerForm;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Volt\Component;
use Livewire\WithPagination;

new class extends Component
{
    use WithPagination;

    public ServerForm $form;

    public function mount()
    {
        $this->form->setOrganization($this->organization);
    }

    #[Computed]
    public function organization()
    {
        return Auth::user()->currentOrganization;
    }

    #[Computed]
    public function servers()
    {
        return $this->organization->servers()
            ->orderByDesc('created_at')
            ->paginate(10);
    }

    public function save()
    {
        $this->form->store();
    }
}; ?>

<div>
    <header class="flex flex-wrap justify-between items-center gap-4">
        <flux:heading size="xl">Servers</flux:heading>
        <flux:modal.trigger name="add-server">
            <flux:button variant="primary">Add server</flux:button>
        </flux:modal.trigger>
    </header>

    <div class="mt-8">
        <flux:table :paginate="$this->servers" wire:poll>
            <flux:table.columns>
                <flux:table.column>Name</flux:table.column>
                <flux:table.column>IP Address</flux:table.column>
                <flux:table.column align="end">Status</flux:table.column>
            </flux:table.columns>
            <flux:table.rows>
                @foreach ($this->servers as $server)
                    <flux:table.row :key="$server->id">
                        <flux:table.cell>
                            <flux:link :href="route('servers.show', $server)" wire:navigate>
                                {{ $server->name }}
                            </flux:link>
                        </flux:table.cell>
                        <flux:table.cell>{{ $server->ip_address }}</flux:table.cell>
                        <flux:table.cell align="end">
                            <flux:badge
                                :color="$server->status_color"
                                size="sm"
                                inset="top bottom"
                                @class(['animate-pulse' => $server->is_provisioning])
                            >
                                {{ $server->status_formatted }}
                            </flux:badge>
                        </flux:table.cell>
                    </flux:table.row>
                @endforeach
            </flux:table.rows>
        </flux:table>
    </div>

    <flux:modal name="add-server" variant="flyout" class="max-w-md">
        <form wire:submit="save" class="space-y-6">
            <flux:heading size="lg">Add server</flux:heading>

            <flux:input
                label="Name"
                wire:model="form.name"
                required
            />

            <flux:input
                label="IP address"
                wire:model="form.ip_address"
                required
            />

            <flux:pillbox
                wire:model="form.ssh_keys"
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

            <div class="flex">
                <flux:spacer />
                <flux:button type="submit" variant="primary">
                    Add server
                </flux:button>
            </div>
        </form>
    </flux:modal>
</div>
