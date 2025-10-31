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

        $this->redirectRoute('ssh-keys.index', navigate: true);
    }

    #[Computed]
    public function organization()
    {
        return Auth::user()->currentOrganization;
    }
}; ?>

<div class="max-w-[512px] mx-auto">
    <flux:link :href="route('ssh-keys.index')" class="inline-flex items-center gap-2 text-sm" variant="subtle" inline wire:navigate>
        <flux:icon.chevron-left variant="micro" />
        SSH keys
    </flux:link>

    <flux:spacer class="mt-4 lg:mt-8" />

    <form wire:submit="save">
        <flux:heading class="text-xl">Add SSH key</flux:heading>

        <flux:spacer class="mt-10" />

        <div class="space-y-6">
            <flux:field>
                <flux:textarea wire:model="public_key" label="Public key" rows="2" required />
                <flux:error name="public_key" />
            </flux:field>
            <flux:field>
                <flux:input wire:model="name" label="Name" required />
                <flux:error name="name" />
            </flux:field>
        </div>

        <flux:spacer class="mt-8" />

        <div class="flex flex-col gap-4">
            <flux:button type="submit" variant="primary" color="zinc" class="w-full">Add SSH key</flux:button>
        </div>
    </form>
</div>

