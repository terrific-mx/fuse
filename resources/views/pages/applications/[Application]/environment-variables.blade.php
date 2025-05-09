<?php

use App\Models\Application;
use Livewire\Volt\Component;

new class extends Component {
    public Application $application;

    public $environmentFile = 'Loading...';
}; ?>

<x-layouts.app>
    @volt('pages.applications.environment-variables')
        <x-applications.layout :application="$application">
            <form wire:submit="save" class="space-y-8 max-w-lg">
                <flux:heading size="lg" level="1">{{ __('Environment Variables') }}</flux:heading>

                <flux:separator />

                <flux:textarea wire:model="environmentFile" :label="__('Environment File')" rows="auto" class="min-h-[424px]" />

                <div class="flex justify-end gap-4">
                    <flux:button type="submit" variant="primary">{{ __('Save file') }}</flux:button>
                </div>
            </form>
        </x-applications.layout>
    @endvolt
</x-layouts.app>
