<?php

use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Rule;
use Livewire\Volt\Component;

new class extends Component
{
    #[Rule('required|string|max:255')]
    public string $name = '';

    #[Rule('required|string')]
    public string $public_key = '';

    public function save()
    {
        $this->validate();

        $this->organization->sshKeys()->create([
            'name' => $this->name,
            'public_key' => $this->public_key,
        ]);

        $this->redirectRoute('ssh-keys', navigate: true);
    }

    #[Computed]
    public function organization()
    {
        return Auth::user()->currentOrganization;
    }
}; ?>

<div>
    <flux:breadcrumbs class="mb-3">
        <flux:breadcrumbs.item :href="route('ssh-keys.index')" wire:navigate>SSH keys</flux:breadcrumbs.item>
        <flux:breadcrumbs.item>Add SSH key</flux:breadcrumbs.item>
    </flux:breadcrumbs>

    <flux:heading size="xl">Add SSH key</flux:heading>

    <form wire:submit="save" class="space-y-6 mt-12">
        <flux:textarea
            wire:model="public_key"
            label="Public key"
            name="public_key"
            required
        />
        <flux:input
            wire:model="name"
            label="Name"
            name="name"
            required
        />
        <div class="flex gap-4">
            <flux:button type="submit" variant="primary">Add SSH key</flux:button>
            <flux:button :href="route('ssh-keys.index')" variant="ghost" wire:navigate>Cancel</flux:button>
        </div>
    </form>
</div>
