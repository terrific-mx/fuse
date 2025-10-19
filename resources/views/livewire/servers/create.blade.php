<?php

use App\Livewire\Forms\ServerForm;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Volt\Component;

new class extends Component
{
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

    public function save()
    {
        $this->form->store();
    }
}; ?>

<div class="max-w-md mx-auto p-6">
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
</div>
