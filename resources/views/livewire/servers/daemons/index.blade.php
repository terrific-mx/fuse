<?php

use App\Models\Server;
use Livewire\Volt\Component;

new class extends Component {
    public Server $server;
}; ?>

<div>
    <header>
        <flux:heading size="xl">{{ $server->name }}</flux:heading>
        @include('partials.server-navbar')
    </header>

    <flux:button>Add daemon</flux:button>
</div>
