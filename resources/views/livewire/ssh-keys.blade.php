<?php

use Livewire\Volt\Component;
use App\Livewire\Forms\SshKeyForm;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;

new class extends Component {
    public SshKeyForm $form;

    public function save()
    {
        $this->form->store($this->organization);
    }

    #[Computed]
    public function organization()
    {
        return Auth::user()->currentOrganization;
    }

    #[Computed]
    public function sshKeys()
    {
        return $this->organization->sshKeys()->latest()->get();
    }
}; ?>

<x-slot:breadcrumbs>
    <flux:breadcrumbs>
        <flux:breadcrumbs.item>{{ __('SSH keys') }}</flux:breadcrumbs.item>
    </flux:breadcrumbs>
</x-slot:breadcrumbs>

<div>
    <header class="mb-8 flex flex-wrap items-end justify-between gap-4">
        <div class="max-sm:w-full sm:flex-1">
            <flux:heading size="lg">
                {{ __('SSH keys') }}
            </flux:heading>
            <flux:text class="mt-2 max-w-prose">
                {{ __('Manage the SSH keys for your organization. SSH keys allow secure, passwordless access to your servers and deployments.') }}
            </flux:text>
        </div>
        <flux:modal.trigger name="add-ssh-key">
            <flux:button variant="primary">
                {{ __('Add SSH key') }}
            </flux:button>
        </flux:modal.trigger>
    </header>

    <div class="mt-8">
        <flux:modal name="add-ssh-key" variant="flyout" class="max-w-md">
            <form wire:submit="save" class="space-y-6">
                <div>
                    <flux:heading size="lg">{{ __('Add SSH key') }}</flux:heading>
                    <flux:text class="mt-2 max-w-prose">
                        {{ __('Provide a name and your public SSH key.') }}
                    </flux:text>
                </div>
                <flux:input
                    wire:model="form.name"
                    label="{{ __('Name') }}"
                    description="{{ __('A label to identify this key.') }}"
                    name="form.name"
                    required
                />
                <flux:input
                    wire:model="form.public_key"
                    label="{{ __('Public key') }}"
                    description="{{ __('Paste your SSH public key here.') }}"
                    name="form.public_key"
                    required
                />
                <div class="flex">
                    <flux:spacer />
                    <flux:button type="submit" variant="primary">
                        {{ __('Add SSH key') }}
                    </flux:button>
                </div>
            </form>
        </flux:modal>
    </div>

    <div class="mt-10">
        <flux:table>
            <flux:table.columns>
                <flux:table.column>{{ __('Name') }}</flux:table.column>
                <flux:table.column>{{ __('Public key') }}</flux:table.column>
                <flux:table.column align="end">{{ __('Added date') }}</flux:table.column>
            </flux:table.columns>
            <flux:table.rows>
                @foreach ($this->sshKeys as $key)
                    <flux:table.row :key="$key->id">
                        <flux:table.cell variant="strong">{{ $key->name }}</flux:table.cell>
                        <flux:table.cell>
                            <div class="max-w-xs">
                                <p class="truncate">{{ $key->masked_public_key }}</p>
                            </div>
                        </flux:table.cell>
                        <flux:table.cell align="end">{{ $key->created_at->format('M j, Y') }}</flux:table.cell>
                    </flux:table.row>
                @endforeach
            </flux:table.rows>
        </flux:table>
    </div>
</div>
