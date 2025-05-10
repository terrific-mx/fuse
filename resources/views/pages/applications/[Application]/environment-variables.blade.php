<?php

use App\Models\Application;
use App\Models\Server;
use App\Scripts\FetchDotEnvFile;
use App\Scripts\SaveDotEnvFile;
use Livewire\Attributes\Lazy;
use Livewire\Attributes\Validate;
use Livewire\Volt\Component;

new class extends Component {
    public Server $server;
    public Application $application;
    public $environmentFileLoaded = false;

    #[Validate('nullable|string')]
    public $environmentVariables = '';

    public function mount()
    {
        $this->server = $this->application->server;
    }

    public function getEnvironmentFile()
    {
        /** @var \App\Models\Task */
        $task = $this->server->run(new FetchDotEnvFile($this->application));

        $this->environmentVariables = $task->output;

        $this->environmentFileLoaded = true;
    }

    public function save()
    {
        $this->validate();

        $this->server->run(new SaveDotEnvFile($this->application, $this->environmentVariables));
    }
}; ?>

<x-layouts.app>
    @volt('pages.applications.environment-variables')
        <x-applications.layout :application="$application">
            <form wire:submit="save" class="space-y-8 max-w-lg">
                <flux:heading size="lg" level="1">{{ __('Environment Variables') }}</flux:heading>

                <flux:separator />

                @if ($environmentFileLoaded)
                    <flux:textarea wire:model="environmentVariables" :label="__('Environment File')" rows="auto" class="font-mono min-h-[424px]" />
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
