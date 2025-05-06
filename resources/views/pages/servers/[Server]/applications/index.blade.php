<?php

use App\Models\Server;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Volt\Component;

new class extends Component {
    public Server $server;
    public ?Collection $applications;

    public function mount()
    {
        $this->applications = $this->server->applications;
    }
}; ?>

<x-layouts.app>
    @volt('pages.servers.applications.index')
        <x-servers.layout :server="$server">
            <section class="space-y-8">
                <div class="flex items-end justify-between gap-4">
                    <flux:heading size="lg" level="2">{{ __('Applications') }}</flux:heading>

                    <flux:button href="/servers/{{ $server->id }}/applications/create" variant="primary" class="-my-2">
                        {{ __('Add Application') }}
                    </flux:button>
                </div>

                <flux:separator />

                <flux:table class="max-w-lg">
                    <flux:table.columns>
                        <flux:table.column>{{ __('Domain') }}</flux:table.column>
                        <flux:table.column>{{ __('Status') }}</flux:table.column>
                        <flux:table.column>{{ __('Actions') }}</flux:table.column>
                    </flux:table.columns>

                    <flux:table.rows>
                        @foreach ($applications as $application)
                            <flux:table.row>
                                <flux:table.cell variant="strong">
                                    <flux:link href="/applications/{{ $application->id }}">{{ $application->domain }}</flux:link>
                                </flux:table.cell>
                                <flux:table.cell><flux:badge color="green" size="sm" inset="top bottom">{{ $application->status }}</flux:badge></flux:table.cell>
                                <flux:table.cell>
                                    <flux:link href="/applications/{{ $application->id }}/delete">{{ __('Delete') }}</flux:link>
                                </flux:table.cell>
                            </flux:table.row>
                        @endforeach
                    </flux:table.rows>
                </flux:table>
            </section>
        </x-servers.layout>
    @endvolt
</x-layouts.app>
