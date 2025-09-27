<?php

use App\Models\Server;
use Livewire\Volt\Component;

new class extends Component {
    public Server $server;
}; ?>

<div class="space-y-12">
    @include('partials.server-navbar', ['server' => $server])

    <section class="space-y-6">
        <header>
            <flux:heading size="lg">{{ __('Add daemon') }}</flux:heading>
            <flux:text class="mt-2">{{ __('Create a new daemon process for your server.') }}</flux:text>
        </header>

        <flux:input :label="__('Command to run')" />
        <flux:input :label="__('Directory')" :badge="__('Optional')" />
        <flux:input :label="__('User to run as')" value="fuse" />
        <flux:input :label="__('Number of processes')" type="number" min="1" value="1" />
        <flux:input :label="__('Stop wait')" :badge="__('Seconds')" type="number" min="0" value="10" />
        <flux:input :label="__('Stop signal')" value="TERM" />

        <flux:button variant="primary">{{ __('Add Daemon') }}</flux:button>
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
                <flux:table.row>
                    <flux:table.cell>php artisan queue:work</flux:table.cell>
                    <flux:table.cell>/var/www</flux:table.cell>
                    <flux:table.cell>www-data</flux:table.cell>
                    <flux:table.cell>2</flux:table.cell>
                    <flux:table.cell>10</flux:table.cell>
                    <flux:table.cell>SIGTERM</flux:table.cell>
                    <flux:table.cell><flux:badge color="green" size="sm" inset="top bottom">Active</flux:badge></flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell>node server.js</flux:table.cell>
                    <flux:table.cell>/home/nodeuser</flux:table.cell>
                    <flux:table.cell>nodeuser</flux:table.cell>
                    <flux:table.cell>1</flux:table.cell>
                    <flux:table.cell>5</flux:table.cell>
                    <flux:table.cell>SIGINT</flux:table.cell>
                    <flux:table.cell><flux:badge color="zinc" size="sm" inset="top bottom">Inactive</flux:badge></flux:table.cell>
                </flux:table.row>
            </flux:table.rows>
        </flux:table>
    </section>
</div>
