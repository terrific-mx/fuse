<?php

use App\Livewire\Forms\SshKeyForm;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Volt\Component;

new class extends Component
{
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

<div>
    <header class="flex flex-wrap justify-between items-center gap-4">
        <flux:heading size="xl">SSH keys</flux:heading>
        <flux:modal.trigger name="add-ssh-key">
            <flux:button variant="primary">Add SSH key</flux:button>
        </flux:modal.trigger>
    </header>

    <div class="mt-8">
        <flux:table>
            <flux:table.columns>
                <flux:table.column>Name</flux:table.column>
                <flux:table.column>Public key</flux:table.column>
                <flux:table.column align="end">Added date</flux:table.column>
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

    <flux:modal name="add-ssh-key" variant="flyout" class="max-w-md">
        <form wire:submit="save" class="space-y-6">
            <flux:heading size="lg">Add SSH key</flux:heading>
            <flux:input
                wire:model="form.name"
                label="Name"
                name="form.name"
                required
            />
            <flux:input
                wire:model="form.public_key"
                label="Public key"
                name="form.public_key"
                required
            />
            <div class="flex">
                <flux:spacer />
                <flux:button type="submit" variant="primary">Add SSH key</flux:button>
            </div>
        </form>
    </flux:modal>
</div>
