<?php

use App\Models\Application;
use Livewire\Volt\Component;

new class extends Component {
    public Application $application;
}; ?>

<x-layouts.app>
    @volt('pages.applications.deployment-settings')
        <x-applications.layout :application="$application">
            <form wire:submit="save" class="space-y-8 max-w-lg">
                <flux:heading size="lg" level="1">{{ __('Deployment settings') }}</flux:heading>

                <flux:separator />

                <flux:input type="number" wire:model="releasesToRetain" :label="__('Number of Releases to Retain')" />
                <flux:textarea type="text" wire:model="sharedDirectories" :label="__('Shared Directories')" rows="auto" />
                <flux:textarea type="text" wire:model="sharedFiles" :label="__('Shared Files')" rows="auto" />
                <flux:textarea type="text" wire:model="writableDirectories" :label="__('Writable Directories')" rows="auto" />
                <flux:textarea type="text" wire:model="beforeUpdateHook" :label="__('Hook for Before Updating Repository')" rows="auto" />
                <flux:textarea type="text" wire:model="afterUpdateHook" :label="__('Hook for After Updating Repository')" rows="auto" />
                <flux:textarea type="text" wire:model="beforeActivateHook" :label="__('Hook for Before Activating New Release')" rows="auto" />
                <flux:textarea type="text" wire:model="afterActivateHook" :label="__('Hook for After Activating New Release')" rows="auto" />

                <div class="flex justify-end gap-4">
                    <flux:button variant="primary" type="submit">{{ __('Save settings') }}</flux:button>
                </div>
            </form>
        </x-applications.layout>
    @endvolt
</x-layouts.app>
