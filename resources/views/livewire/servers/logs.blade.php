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
            <flux:heading size="lg">{{ __('Server Logs') }}</flux:heading>
            <flux:text class="mt-2">{{ __('Access important server log files.') }}</flux:text>
        </header>
        <flux:table>
            <flux:table.columns>
                <flux:table.column>{{ __('Log File') }}</flux:table.column>
                <flux:table.column>{{ __('Description') }}</flux:table.column>
                <flux:table.column></flux:table.column>
            </flux:table.columns>
            <flux:table.rows>
                <flux:table.row>
                    <flux:table.cell>Caddy Access Log</flux:table.cell>
                    <flux:table.cell>{{ __('Web server access log') }}</flux:table.cell>
                    <flux:table.cell align="end">
                        <flux:button size="sm">{{ __('Access') }}</flux:button>
                    </flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell>Caddy Error Log</flux:table.cell>
                    <flux:table.cell>{{ __('Web server error log') }}</flux:table.cell>
                    <flux:table.cell align="end">
                        <flux:button size="sm">{{ __('Access') }}</flux:button>
                    </flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell>MySQL Error Log</flux:table.cell>
                    <flux:table.cell>{{ __('Database error log') }}</flux:table.cell>
                    <flux:table.cell align="end">
                        <flux:button size="sm">{{ __('Access') }}</flux:button>
                    </flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell>Redis Server Log</flux:table.cell>
                    <flux:table.cell>{{ __('Redis server log') }}</flux:table.cell>
                    <flux:table.cell align="end">
                        <flux:button size="sm">{{ __('Access') }}</flux:button>
                    </flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell>PHP 8.1 FPM Log</flux:table.cell>
                    <flux:table.cell>{{ __('PHP 8.1 process manager log') }}</flux:table.cell>
                    <flux:table.cell align="end">
                        <flux:button size="sm">{{ __('Access') }}</flux:button>
                    </flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell>PHP 8.2 FPM Log</flux:table.cell>
                    <flux:table.cell>{{ __('PHP 8.2 process manager log') }}</flux:table.cell>
                    <flux:table.cell align="end">
                        <flux:button size="sm">{{ __('Access') }}</flux:button>
                    </flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell>PHP 8.3 FPM Log</flux:table.cell>
                    <flux:table.cell>{{ __('PHP 8.3 process manager log') }}</flux:table.cell>
                    <flux:table.cell align="end">
                        <flux:button size="sm">{{ __('Access') }}</flux:button>
                    </flux:table.cell>
                </flux:table.row>
            </flux:table.rows>
        </flux:table>
    </section>
</div>
