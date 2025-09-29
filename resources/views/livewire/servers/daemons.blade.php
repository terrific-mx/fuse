<?php

use App\Models\Server;
use Livewire\Volt\Component;

use App\Livewire\Forms\DaemonForm;

new class extends Component {
    public Server $server;

    public DaemonForm $form;

    public function save()
    {
        $this->form->store($this->server);
    }
}; ?>

<div class="space-y-12">
    @include('partials.server-navbar', ['server' => $server])

    <section class="space-y-6">
        <header>
            <flux:heading size="lg">{{ __('Add daemon') }}</flux:heading>
            <flux:text class="mt-2">{{ __('Create a new daemon process for your server.') }}</flux:text>
        </header>

        <form wire:submit="save" class="space-y-6">
            <flux:input :label="__('Command to run')" wire:model.defer="form.command" />
            <flux:input :label="__('Directory')" :badge="__('Optional')" wire:model.defer="form.directory" />
            <flux:input :label="__('User to run as')" wire:model.defer="form.user" />
            <flux:input :label="__('Number of processes')" type="number" min="1" wire:model.defer="form.processes" />
            <flux:input :label="__('Stop wait')" :badge="__('Seconds')" type="number" min="0" wire:model.defer="form.stop_wait" />
            <flux:input :label="__('Stop signal')" wire:model.defer="form.stop_signal" />

            <flux:button variant="primary" type="submit">{{ __('Add Daemon') }}</flux:button>
        </form>
    </section>

    <section class="space-y-6">
        <header>
            <flux:heading size="lg">{{ __('Daemons') }}</flux:heading>
            <flux:text class="mt-2">{{ __('List of daemons on this server.') }}</flux:text>
        </header>

         <flux:table>
            <flux:table.columns>
                <flux:table.column>{{ __('Command') }}</flux:table.column>
                <flux:table.column>{{ __('Directory') }}</flux:table.column>
                <flux:table.column>{{ __('User') }}</flux:table.column>
                <flux:table.column>{{ __('Processes') }}</flux:table.column>
                <flux:table.column>{{ __('Stop Wait (s)') }}</flux:table.column>
                <flux:table.column>{{ __('Stop Signal') }}</flux:table.column>
                <flux:table.column>{{ __('Status') }}</flux:table.column>
            </flux:table.columns>
            <flux:table.rows>
                @foreach ($server->daemons as $daemon)
                    <flux:table.row>
                        <flux:table.cell>{{ $daemon->command }}</flux:table.cell>
                        <flux:table.cell>{{ $daemon->directory }}</flux:table.cell>
                        <flux:table.cell>{{ $daemon->user }}</flux:table.cell>
                        <flux:table.cell>{{ $daemon->processes }}</flux:table.cell>
                        <flux:table.cell>{{ $daemon->stop_wait }}</flux:table.cell>
                        <flux:table.cell>{{ $daemon->stop_signal }}</flux:table.cell>
                        <flux:table.cell>
                            <flux:badge color="zinc" size="sm" inset="top bottom">Inactive</flux:badge>
                        </flux:table.cell>
                    </flux:table.row>
                @endforeach
            </flux:table.rows>
        </flux:table>
    </section>
</div>
