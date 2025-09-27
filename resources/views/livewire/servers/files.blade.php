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
            <flux:heading size="lg">{{ __('Configuration Files') }}</flux:heading>
            <flux:text class="mt-2">{{ __('Access and edit important server configuration files.') }}</flux:text>
        </header>
        <flux:table>
            <flux:table.columns>
                <flux:table.column>{{ __('File') }}</flux:table.column>
                <flux:table.column>{{ __('Description') }}</flux:table.column>
                <flux:table.column></flux:table.column>
            </flux:table.columns>
            <flux:table.rows>
                <flux:table.row>
                    <flux:table.cell>Caddyfile</flux:table.cell>
                    <flux:table.cell>{{ __('Web server configuration') }}</flux:table.cell>
                    <flux:table.cell align="end">
                        <flux:button size="sm">{{ __('Edit') }}</flux:button>
                    </flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell>MySQL config file</flux:table.cell>
                    <flux:table.cell>{{ __('Database configuration') }}</flux:table.cell>
                    <flux:table.cell align="end">
                        <flux:button size="sm">{{ __('Edit') }}</flux:button>
                    </flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell>PHP 8.1 ini File</flux:table.cell>
                    <flux:table.cell>{{ __('PHP 8.1 settings') }}</flux:table.cell>
                    <flux:table.cell align="end">
                        <flux:button size="sm">{{ __('Edit') }}</flux:button>
                    </flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell>PHP 8.1 FPM Configuration</flux:table.cell>
                    <flux:table.cell>{{ __('PHP 8.1 process manager') }}</flux:table.cell>
                    <flux:table.cell align="end">
                        <flux:button size="sm">{{ __('Edit') }}</flux:button>
                    </flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell>PHP 8.2 ini File</flux:table.cell>
                    <flux:table.cell>{{ __('PHP 8.2 settings') }}</flux:table.cell>
                    <flux:table.cell align="end">
                        <flux:button size="sm">{{ __('Edit') }}</flux:button>
                    </flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell>PHP 8.2 FPM Configuration</flux:table.cell>
                    <flux:table.cell>{{ __('PHP 8.2 process manager') }}</flux:table.cell>
                    <flux:table.cell align="end">
                        <flux:button size="sm">{{ __('Edit') }}</flux:button>
                    </flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell>PHP 8.3 ini File</flux:table.cell>
                    <flux:table.cell>{{ __('PHP 8.3 settings') }}</flux:table.cell>
                    <flux:table.cell align="end">
                        <flux:button size="sm">{{ __('Edit') }}</flux:button>
                    </flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell>PHP 8.3 FPM Configuration</flux:table.cell>
                    <flux:table.cell>{{ __('PHP 8.3 process manager') }}</flux:table.cell>
                    <flux:table.cell align="end">
                        <flux:button size="sm">{{ __('Edit') }}</flux:button>
                    </flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell>auth.json</flux:table.cell>
                    <flux:table.cell>{{ __('Composer authentication configuration') }}</flux:table.cell>
                    <flux:table.cell align="end">
                        <flux:button size="sm">{{ __('Edit') }}</flux:button>
                    </flux:table.cell>
                </flux:table.row>
            </flux:table.rows>
        </flux:table>
    </section>
</div>
