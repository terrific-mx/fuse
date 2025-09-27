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
            <flux:heading size="lg">{{ __('Services') }}</flux:heading>
            <flux:text class="mt-2">{{ __('List of installed services on this server.') }}</flux:text>
        </header>

        <flux:table>
            <flux:table.columns>
                <flux:table.column>{{ __('Service') }}</flux:table.column>
                <flux:table.column>{{ __('Status') }}</flux:table.column>
                <flux:table.column></flux:table.column>
            </flux:table.columns>
            <flux:table.rows>
                <flux:table.row>
                    <flux:table.cell>Caddy</flux:table.cell>
                    <flux:table.cell><flux:badge color="green" size="sm" inset="top bottom">{{ __('Active') }}</flux:badge></flux:table.cell>
                    <flux:table.cell align="end">
                        <flux:button size="sm">{{ __('Restart') }}</flux:button>
                    </flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell>MySQL</flux:table.cell>
                    <flux:table.cell><flux:badge color="green" size="sm" inset="top bottom">{{ __('Active') }}</flux:badge></flux:table.cell>
                    <flux:table.cell align="end">
                        <flux:button size="sm">{{ __('Restart') }}</flux:button>
                    </flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell>PHP 8.1</flux:table.cell>
                    <flux:table.cell><flux:badge color="green" size="sm" inset="top bottom">{{ __('Active') }}</flux:badge></flux:table.cell>
                    <flux:table.cell align="end">
                        <flux:button size="sm">{{ __('Restart') }}</flux:button>
                    </flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell>PHP 8.2</flux:table.cell>
                    <flux:table.cell><flux:badge color="green" size="sm" inset="top bottom">{{ __('Active') }}</flux:badge></flux:table.cell>
                    <flux:table.cell align="end">
                        <flux:button size="sm">{{ __('Restart') }}</flux:button>
                    </flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell>PHP 8.3</flux:table.cell>
                    <flux:table.cell><flux:badge color="green" size="sm" inset="top bottom">{{ __('Active') }}</flux:badge></flux:table.cell>
                    <flux:table.cell align="end">
                        <flux:button size="sm">{{ __('Restart') }}</flux:button>
                    </flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell>PHP 8.4</flux:table.cell>
                    <flux:table.cell><flux:badge color="green" size="sm" inset="top bottom">{{ __('Active') }}</flux:badge></flux:table.cell>
                    <flux:table.cell align="end">
                        <flux:button size="sm">{{ __('Restart') }}</flux:button>
                    </flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell>Redis 6</flux:table.cell>
                    <flux:table.cell><flux:badge color="green" size="sm" inset="top bottom">{{ __('Active') }}</flux:badge></flux:table.cell>
                    <flux:table.cell align="end">
                        <flux:button size="sm">{{ __('Restart') }}</flux:button>
                    </flux:table.cell>
                </flux:table.row>
            </flux:table.rows>
        </flux:table>
    </section>
</div>
