<?php

use App\Jobs\DeleteApplication;
use App\Models\Application;
use App\Scripts\DeleteFolder;
use App\Scripts\UpdateCaddyImports;
use Livewire\Volt\Component;

new class extends Component {
    public Application $application;

    public function delete()
    {
        $this->authorize($this->application, 'delete');

        $server = $this->application->server;

        DeleteApplication::dispatch($server, $this->application->path());

        $this->application->delete();

        return $this->redirect("/servers/{$server->id}");
    }
}; ?>

<x-layouts.app>
    @volt('pages.applications.delete')
        <x-applications.layout :application="$application">
            <form wire:submit="delete" class="space-y-8 max-w-lg">
                <flux:heading size="lg" level="2">{{ __('Delete application confirmation') }}</flux:heading>

                <flux:separator />

                <div class="flex justify-end gap-4">
                    <flux:button variant="ghost" href="/servers/{{ $application->server->id }}">{{ __('Cancel') }}</flux:button>
                    <flux:button variant="primary" type="submit">{{ __('Delete') }}</flux:button>
                </div>
            </form>
        </x-applications.layout>
    @endvolt
</x-layouts.app>
