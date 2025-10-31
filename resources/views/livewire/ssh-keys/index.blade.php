<?php

use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Volt\Component;
use Livewire\WithPagination;

new class extends Component
{
    use WithPagination;

    #[Computed]
    public function organization()
    {
        return Auth::user()->currentOrganization;
    }

    #[Computed]
    public function sshKeys()
    {
        return $this->organization->sshKeys()->orderByDesc('created_at')->paginate(10);
    }
}; ?>

<div class="max-w-3xl mx-auto">
    <header class="flex items-center">
        <flux:heading class="text-xl">All SSH keys</flux:heading>
        <flux:spacer />
        <flux:button :href="route('ssh-keys.create')" variant="primary" color="zinc" size="sm" icon="plus" wire:navigate>
            New SSH key
        </flux:button>
    </header>

    <flux:separator class="mt-6" />

    <flux:table :paginate="$this->sshKeys">
        <flux:table.rows>
            @foreach ($this->sshKeys as $key)
                <flux:table.row :key="$key->id">
                    <flux:table.cell class="w-full">
                        <flux:link :href="route('ssh-keys.edit', $key)" :accent="false" wire:navigate>{{ $key->name }}</flux:link>
                    </flux:table.cell>
                    <flux:table.cell>
                        <div class="max-w-xs text-xs">
                            <p class="truncate">{{ $key->masked_public_key }}</p>
                        </div>
                    </flux:table.cell>
                    <flux:table.cell align="end" class="text-xs">
                        {{ $key->created_at->format('M d') }}
                    </flux:table.cell>
                </flux:table.row>
            @endforeach
        </flux:table.rows>
    </flux:table>
</div>
