<?php

use App\Models\Application;
use Livewire\Volt\Component;

new class extends Component {
    public Application $application;

    public $releases_to_retain = 10;
    public $shared_directories = '';
    public $shared_files = '';
    public $writable_directories = '';
    public $before_update_hook = '';
    public $after_update_hook = '';
    public $before_activate_hook = '';
    public $after_activate_hook = '';

    public function mount()
    {
        $this->releases_to_retain = $this->application->releases_to_retain;
        $this->shared_directories = collect($this->application->shared_directories)->implode(PHP_EOL);
        $this->shared_files = collect($this->application->shared_files)->implode(PHP_EOL);
        $this->writable_directories = collect($this->application->writable_directories)->implode(PHP_EOL);
        $this->before_update_hook = $this->application->before_update_hook;
        $this->after_update_hook = $this->application->after_update_hook;
        $this->before_activate_hook = $this->application->before_activate_hook;
        $this->after_activate_hook = $this->application->after_activate_hook;
    }

    public function save()
    {
        $this->application->update([
            'releases_to_retain' => $this->releases_to_retain,
            'shared_directories' => explode(PHP_EOL, $this->shared_directories),
            'shared_files' => explode(PHP_EOL, $this->shared_files),
            'writable_directories' => explode(PHP_EOL, $this->writable_directories),
            'before_update_hook' => $this->before_update_hook,
            'after_update_hook' => $this->after_update_hook,
            'before_activate_hook' => $this->before_activate_hook,
            'after_activate_hook' => $this->after_activate_hook,
        ]);
    }
}; ?>

<x-layouts.app>
    @volt('pages.applications.deployment-settings')
        <x-applications.layout :application="$application">
            <form wire:submit="save" class="space-y-8 max-w-lg">
                <flux:heading size="lg" level="1">{{ __('Deployment settings') }}</flux:heading>

                <flux:separator />

                <flux:input type="number" wire:model="releases_to_retain" :label="__('Number of Releases to Retain')" />
                <flux:textarea type="text" wire:model="shared_directories" :label="__('Shared Directories')" rows="auto" />
                <flux:textarea type="text" wire:model="shared_files" :label="__('Shared Files')" rows="auto" />
                <flux:textarea type="text" wire:model="writable_directories" :label="__('Writable Directories')" rows="auto" />
                <flux:textarea type="text" wire:model="before_update_hook" :label="__('Hook for Before Updating Repository')" rows="auto" />
                <flux:textarea type="text" wire:model="after_update_hook" :label="__('Hook for After Updating Repository')" rows="auto" />
                <flux:textarea type="text" wire:model="before_activate_hook" :label="__('Hook for Before Activating New Release')" rows="auto" />
                <flux:textarea type="text" wire:model="after_activate_hook" :label="__('Hook for After Activating New Release')" rows="auto" />

                <div class="flex justify-end gap-4">
                    <flux:button variant="primary" type="submit">{{ __('Save settings') }}</flux:button>
                </div>
            </form>
        </x-applications.layout>
    @endvolt
</x-layouts.app>
