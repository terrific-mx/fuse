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
            <flux:heading size="lg">{{ __('Site Logs') }}</flux:heading>
            <flux:text class="mt-2">{{ __('Review important site log files, including all requests made to this site.') }}</flux:text>
        </header>
        <flux:table>
            <flux:table.columns>
                <flux:table.column class="w-full">{{ __('Log File') }}</flux:table.column>
                <flux:table.column>{{ __('Description') }}</flux:table.column>
                <flux:table.column></flux:table.column>
            </flux:table.columns>
            <flux:table.rows>
                <flux:table.row>
                    <flux:table.cell>Caddy Access Log</flux:table.cell>
                    <flux:table.cell>{{ __('All requests made to this site') }}</flux:table.cell>
                    <flux:table.cell align="end">
                        <flux:button size="sm">{{ __('Access') }}</flux:button>
                    </flux:table.cell>
                </flux:table.row>
            </flux:table.rows>
        </flux:table>
    </section>
</div>
