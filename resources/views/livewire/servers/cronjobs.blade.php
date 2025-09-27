<?php

use App\Models\Server;
use Livewire\Volt\Component;

new class extends Component {
    public Server $server;
}; ?>

<div class="space-y-12">
    @include('partials.server-navbar', ['server' => $server])

    <section class="space-y-6" x-data="{ custom: false }">
        <header>
            <flux:heading size="lg">{{ __('Add cronjob') }}</flux:heading>
            <flux:text class="mt-2">{{ __('Create a new cronjob for your server.') }}</flux:text>
        </header>

        <flux:input :label="__('Command to run')" />
        <flux:input :label="__('User to run the command')" />

        <flux:switch label="{{ __('Custom cron expression') }}" x-model="custom" />

        <div x-show="!custom">
            <flux:select :label="__('Frequency')">
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
            <flux:input :label="__('Custom cron expression')" placeholder="* * * * *" />
        </div>

        <flux:button variant="primary">{{ __('Add Cronjob') }}</flux:button>
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
                <flux:table.column>{{ __('Status') }}</flux:table.column>
            </flux:table.columns>
            <flux:table.rows>
                <flux:table.row>
                    <flux:table.cell>php artisan schedule:run</flux:table.cell>
                    <flux:table.cell>root</flux:table.cell>
                    <flux:table.cell>* * * * *</flux:table.cell>
                    <flux:table.cell><flux:badge color="green" size="sm" inset="top bottom">Active</flux:badge></flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell>backup.sh</flux:table.cell>
                    <flux:table.cell>backup</flux:table.cell>
                    <flux:table.cell>0 0 * * *</flux:table.cell>
                    <flux:table.cell><flux:badge color="zinc" size="sm" inset="top bottom">Inactive</flux:badge></flux:table.cell>
                </flux:table.row>
            </flux:table.rows>
        </flux:table>
    </section>
</div>
