<?php

use App\Models\Server;
use App\Models\Site;
use Livewire\Volt\Component;

new class extends Component {
    public Server $server;

    public Site $site;
}; ?>

<div class="space-y-12">
    @include('partials.site-navbar', ['server' => $server, 'site' => $site])

    <section class="space-y-6">
        <header>
            <flux:heading size="lg">{{ __('Site Files') }}</flux:heading>
            <flux:text class="mt-2">{{ __('Access and edit important site configuration files.') }}</flux:text>
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
                    <flux:table.cell>{{ __('Defines how your site is served by Caddy, including domains, routing, TLS certificates, and request handling.') }}</flux:table.cell>
                    <flux:table.cell align="end">
                        <flux:button size="sm">{{ __('Edit') }}</flux:button>
                    </flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell>.env</flux:table.cell>
                    <flux:table.cell>{{ __('Contains environment variables for your site, such as database credentials, API keys, and other configuration values.') }}</flux:table.cell>
                    <flux:table.cell align="end">
                        <flux:button size="sm">{{ __('Edit') }}</flux:button>
                    </flux:table.cell>
                </flux:table.row>
            </flux:table.rows>
        </flux:table>
    </section>
</div>
