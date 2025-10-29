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

<div>
    <flux:breadcrumbs>
        <flux:breadcrumbs.item :href="route('ssh-keys.index')" wire:navigate>
            SSH Keys
        </flux:breadcrumbs.item>
    </flux:breadcrumbs>

    <flux:spacer class="mt-8" />

    <form wire:submit="save">
        <flux:heading size="xl">Add SSH key</flux:heading>

        <flux:separator class="my-10 mt-6" />

        <section class="grid gap-x-8 gap-y-6 sm:grid-cols-2">
            <div>
                <flux:label>Public key</flux:label>
            </div>
            <div>
                <flux:textarea wire:model="public_key" rows="2" required />
                <flux:error name="public_key" />
            </div>
        </section>

        <flux:separator variant="subtle" class="my-10" />

        <section class="grid gap-x-8 gap-y-6 sm:grid-cols-2">
            <div>
                <flux:label>Name</flux:label>
            </div>
            <div>
                <flux:input wire:model="name" required />
                <flux:error name="name" />
            </div>
        </section>

        <flux:separator variant="subtle" class="my-10" />

        <div class="flex justify-end gap-4">
            <flux:button :href="route('ssh-keys.index')" variant="ghost" wire:navigate>Cancel</flux:button>
            <flux:button type="submit" variant="primary" color="zinc">Add SSH key</flux:button>
        </div>
    </form>
</div>
