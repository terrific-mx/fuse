<?php

use App\Models\Server;
use Livewire\Volt\Component;
use App\Livewire\Forms\CronjobForm;

new class extends Component {
    public Server $server;
    public CronjobForm $form;

    public function save()
    {
        $this->form->store($this->server);
    }
}; ?>

<div class="space-y-12">
    @include('partials.server-navbar', ['server' => $server])

    <section class="space-y-6" x-data="{ custom: false }">
        <header>
            <flux:heading size="lg">{{ __('Add cronjob') }}</flux:heading>
            <flux:text class="mt-2">{{ __('Create a new cronjob for your server.') }}</flux:text>
        </header>

        <form wire:submit="save" class="space-y-6">
            <flux:input :label="__('Command to run')" wire:model="form.command" />

            <flux:input :label="__('User to run the command')" wire:model="form.user" />

            <flux:switch label="{{ __('Custom cron expression') }}" x-model="custom" />

            <div x-show="!custom">
                <flux:select :label="__('Frequency')" wire:model="form.expression">
                    <flux:select.option value="* * * * *">{{ __('Every minute') }}</flux:select.option>
                    <flux:select.option value="*/5 * * * *">{{ __('Every 5 minutes') }}</flux:select.option>
                    <flux:select.option value="0 * * * *">{{ __('Hourly') }}</flux:select.option>
                    <flux:select.option value="0 0 * * *">{{ __('Daily') }}</flux:select.option>
                    <flux:select.option value="0 0 * * 0">{{ __('Weekly') }}</flux:select.option>
                    <flux:select.option value="0 0 1 * *">{{ __('Monthly') }}</flux:select.option>
                    <flux:select.option value="@reboot">{{ __('On reboot') }}</flux:select.option>
                </flux:select>
            </div>

            <div x-show="custom">
                <flux:input :label="__('Custom cron expression')" placeholder="* * * * *" wire:model="form.expression" />
            </div>

            <flux:button variant="primary" type="submit">{{ __('Add Cronjob') }}</flux:button>
        </form>
    </section>

    <section class="space-y-6">
        <header>
            <flux:heading size="lg">{{ __('Cronjobs') }}</flux:heading>
            <flux:text class="mt-2">{{ __('List of cronjobs on this server.') }}</flux:text>
        </header>

        <flux:table>
            <flux:table.columns>
                <flux:table.column>{{ __('Command') }}</flux:table.column>
                <flux:table.column>{{ __('User') }}</flux:table.column>
                <flux:table.column>{{ __('Expression') }}</flux:table.column>
            </flux:table.columns>
            <flux:table.rows>
                @foreach($server->cronjobs as $cronjob)
                    <flux:table.row>
                        <flux:table.cell>{{ $cronjob->command }}</flux:table.cell>
                        <flux:table.cell>{{ $cronjob->user }}</flux:table.cell>
                        <flux:table.cell>{{ $cronjob->expression }}</flux:table.cell>
                    </flux:table.row>
                @endforeach
            </flux:table.rows>
        </flux:table>
    </section>
</div>
