<?php

use App\Models\Server;
use Livewire\Volt\Component;

new class extends Component
{
    public Server $server;

    public function mount()
    {
        $this->authorize('view', $this->server);
    }
}; ?>

<div class="space-y-8">
    <header class="mb-6 space-y-2">
        <flux:heading size="xl">{{ $server->name }}</flux:heading>
        @include('partials.server-navbar')
    </header>

    <div class="mb-4">
        <flux:button :href="route('servers.databases.create', $server)">Create database</flux:button>
    </div>

    <div>
        {{-- Database table/list goes here --}}
    </div>
</div>
