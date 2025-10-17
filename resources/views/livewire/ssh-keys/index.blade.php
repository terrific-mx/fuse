<?php

use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Volt\Component;

new class extends Component
{
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
        <flux:button :href="route('ssh-keys.create')" variant="primary" size="sm" wire:navigate>Add SSH key</flux:button>
    </header>

    <div class="mt-12">
        <flux:table>
            <flux:table.columns>
                <flux:table.column>Name</flux:table.column>
                <flux:table.column>Public key</flux:table.column>
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
                    </flux:table.row>
                @endforeach
            </flux:table.rows>
        </flux:table>
    </div>
</div>
