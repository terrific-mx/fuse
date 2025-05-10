<?php

use App\Models\Application;
use App\Scripts\FetchEnvFile;
use Livewire\Attributes\Lazy;
use Livewire\Volt\Component;

new class extends Component {
    public Application $application;

    public $environmentFile = '';
    public $environmentFileLoaded = false;

    public function getEnvironmentFile()
    {
        /** @var \App\Models\Server */
        $server = $this->application->server;

        /** @var \App\Models\Task */
        $task = $server->run(new FetchEnvFile($this->application));

        $this->environmentFile = $task->output;

        $this->environmentFileLoaded = true;
    }
}; ?>

<x-layouts.app>
    @volt('pages.applications.environment-variables')
        <x-applications.layout :application="$application">
            <form wire:submit="save" class="space-y-8 max-w-lg">
                <flux:heading size="lg" level="1">{{ __('Environment Variables') }}</flux:heading>

                <flux:separator />

                @if ($environmentFileLoaded)
                    <flux:textarea wire:model="environmentFile" :label="__('Environment File')" rows="auto" class="font-mono min-h-[424px]" />
                @else
                    <flux:input x-init="$wire.getEnvironmentFile()" :label="__('Environment File')" icon:trailing="loading" />
                @endif

                <div class="flex justify-end gap-4">
                    <flux:button type="submit" variant="primary">{{ __('Save file') }}</flux:button>
                </div>
            </form>
        </x-applications.layout>
    @endvolt
</x-layouts.app>
