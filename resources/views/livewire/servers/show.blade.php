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

<div>
    <header>
        <flux:heading size="xl">{{ $server->name }}</flux:heading>
        @include('partials.server-navbar')
    </header>
    <section class="space-y-6 max-w-lg mt-8">
        <flux:input label="Name" value="{{ $server->name }}" variant="filled" readonly />
        <flux:input label="Ip Address" value="{{ $server->ip_address }}" variant="filled" readonly />
        <flux:input label="Sudo Password" value="{{ $server->sudo_password }}" type="password" variant="filled" readonly viewable copyable />
        <flux:input label="Database Password" value="{{ $server->database_password }}" type="password" variant="filled" readonly viewable copyable />
    </section>
</div>
