<?php

use Laravel\WorkOS\Http\Middleware\ValidateSessionWithWorkOS;
use Livewire\Volt\Component;

use function Laravel\Folio\middleware;

middleware(['auth', ValidateSessionWithWorkOS::class]);

new class extends Component {

}; ?>

<x-layouts.app>
    @volt('pages.ssh-keys.create')
        <form wire:submit="add" class="space-y-8 mx-auto max-w-lg">
            <flux:heading size="xl" level="1">{{ __('Add a SSH Key') }}</flux:heading>

            <flux:separator />

            <flux:input wire:model="name" :label="__('Name')" />

            <flux:textarea
                wire:model="public_key"
                :label="__('Public Key')"
                :placeholder="__('Paste your public key here (e.g., ssh-rsa AAAAB3NzaC1yc2E...)')"
            />

            <div class="flex justify-end gap-4">
                <flux:button variant="ghost" href="/ssh-keys" wire:navigate>{{ __('Cancel') }}</flux:button>
                <flux:button variant="primary" type="submit">{{ __('Add Key') }}</flux:button>
            </div>
        </form>
    @endvolt
</x-layouts.app>
