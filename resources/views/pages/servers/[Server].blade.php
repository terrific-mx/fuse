<?php

use App\Models\Server;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Volt\Component;

new class extends Component {
    public Server $server;
    public ?Collection $sites;

    public function mount()
    {
        $this->sites = $this->server->sites;
    }
}; ?>

<x-layouts.app>
    @volt('pages.servers.show')
        <section class="space-y-6">
            <div>
                <flux:link href="/servers" class="text-sm">
                    {{ __('Back') }}
                </flux:link>
            </div>

            <flux:heading>{{ __('Server Name') }}: {{ $server->name }}</flux:heading>

            <flux:button href="/servers/{{ $server->id }}/sites/create">
                {{ __('Install site') }}
            </flux:button>

            <flux:table>
                <flux:table.columns>
                    <flux:table.column>{{ __('Domain') }}</flux:table.column>
                    <flux:table.column>{{ __('Status') }}</flux:table.column>
                    <flux:table.column>{{ __('Actions') }}</flux:table.column>
                </flux:table.columns>

                <flux:table.rows>
                    @foreach ($sites as $site)
                        <flux:table.row>
                            <flux:table.cell variant="strong">
                                <flux:link href="/sites/{{ $site->id }}">{{ $site->domain }}</flux:link>
                            </flux:table.cell>
                            <flux:table.cell><flux:badge color="green" size="sm" inset="top bottom">{{ $site->status }}</flux:badge></flux:table.cell>
                            <flux:table.cell>
                                <flux:link href="/sites/{{ $site->id }}/delete">{{ __('Delete') }}</flux:link>
                            </flux:table.cell>
                        </flux:table.row>
                    @endforeach
                </flux:table.rows>
            </flux:table>
        </section>
    @endvolt
</x-layouts.app>
