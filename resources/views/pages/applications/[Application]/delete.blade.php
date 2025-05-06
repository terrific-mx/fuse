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
    <section class="space-y-6">
            <div>
                <flux:link href="/servers/{{ $application->server->id }}" class="text-sm">
                    {{ __('Back') }}
                </flux:link>
            </div>

            <flux:heading>{{ __('Deleting application') }}: {{ $application->domain }}</flux:heading>

            <form wire:submit="delete">
                <flux:button type="submit">Delete</flux:button>
            </form>
        </section>
    @endvolt
</x-layouts.app>
