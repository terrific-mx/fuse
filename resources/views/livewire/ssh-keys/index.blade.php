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
    <header class="flex items-center">
        <flux:heading size="xl">SSH Keys</flux:heading>
        <flux:spacer />
        <flux:button :href="route('ssh-keys.create')" variant="primary" color="zinc" wire:navigate>
            New SSH Key
        </flux:button>
    </header>

    <flux:spacer class="mt-8" />

    <div>
        <flux:table>
            <flux:table.columns>
                <flux:table.column>Name</flux:table.column>
                <flux:table.column>Public key</flux:table.column>
            </flux:table.columns>
            <flux:table.rows>
                @foreach ($this->sshKeys as $key)
                    <flux:table.row :key="$key->id">
                        <flux:table.cell>
                            <flux:link :href="route('ssh-keys.edit', $key)" wire:navigate>
                                {{ $key->name }}
                            </flux:link>
                        </flux:table.cell>
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
