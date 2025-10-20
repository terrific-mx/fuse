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
    <header class="flex items-center -mt-6 lg:-mt-8 min-h-14">
        <flux:heading size="lg">{{ $server->name }}</flux:heading>
    </header>

    <flux:spacer class="mt-12" />

    <flux:callout icon="server" color="blue">
        <flux:callout.heading>Provision server</flux:callout.heading>
        <flux:callout.text>
            Run this command as <strong>root</strong> on your server. When finished, you can view your server’s status.
        </flux:callout.text>
        <flux:input
            value="wget --no-verbose -O - {{ route('servers.setup-root-ssh', ['server' => $server]) }} | bash"
            readonly
            copyable
            icon="clipboard"
            class="mt-4 font-mono"
        />
        <x-slot name="actions">
            <flux:button href="{{ route('servers.index') }}" wire:navigate>
                View servers
            </flux:button>
        </x-slot>
    </flux:callout>
</div>
