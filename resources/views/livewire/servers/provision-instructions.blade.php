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
    <p>To start provisioning, run the following command as <strong>root</strong> on your server:</p>
    <pre class="bg-gray-100 p-2 rounded text-sm select-all">wget --no-verbose -O - {{ route('servers.setup-root-ssh', ['server' => $server]) }} | bash</pre>
</div>

