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
    <h1>Provision Server: {{ $server->name }}</h1>
    <p>To start provisioning, run the provided script as root on your server.</p>
</div>

