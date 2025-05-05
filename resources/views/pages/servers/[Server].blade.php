<?php

use App\Models\Server;
use Livewire\Volt\Component;

new class extends Component {
    public Server $server;
}; ?>

<x-layouts.app>
    @volt('pages.servers.show')
        <section class="space-y-5">
            <flux:heading>
                {{ $server->name }}
            </flux:heading>
            <flux:button href="/servers/{{ $server->id }}/sites/create">
                {{ __('Add site') }}
            </flux:button>
        </section>
    @endvolt
</x-layouts.app>
