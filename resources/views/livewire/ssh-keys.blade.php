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

<div>
    <form wire:submit="save" class="space-y-6">
        <flux:input
            wire:model="form.name"
            label="{{ __('SSH Key Name') }}"
            name="form.name"
            required
        />
        <flux:input
            wire:model="form.public_key"
            label="{{ __('Public Key') }}"
            name="form.public_key"
            required
        />
        <flux:button type="submit" variant="primary">
            {{ __('Add SSH Key') }}
        </flux:button>
    </form>

    <div class="mt-10">
        <flux:table>
            <flux:table.columns>
                <flux:table.column>{{ __('Name') }}</flux:table.column>
                <flux:table.column>{{ __('Public Key') }}</flux:table.column>
                <flux:table.column>{{ __('Created At') }}</flux:table.column>
            </flux:table.columns>
            <flux:table.rows>
                @foreach ($this->sshKeys as $key)
                    <flux:table.row :key="$key->id">
                        <flux:table.cell>{{ $key->name }}</flux:table.cell>
                        <flux:table.cell>{{ $key->public_key }}</flux:table.cell>
                        <flux:table.cell>{{ $key->created_at->format('Y-m-d H:i') }}</flux:table.cell>
                    </flux:table.row>
                @endforeach
            </flux:table.rows>
        </flux:table>
    </div>
</div>
