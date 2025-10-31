<?php

use App\Models\Server;
use Livewire\Volt\Component;

new #[Title('Server passwords')] class extends Component
{
    public Server $server;

    public function mount()
    {
        $this->authorize('view', $this->server);
    }
}; ?>

<div class="max-w-[512px] mx-auto">
    <flux:link :href="route('servers.show', $server)" class="inline-flex items-center gap-2 text-sm" variant="subtle" inline wire:navigate>
        <flux:icon.chevron-left variant="micro" />
        {{ $server->name }}
    </flux:link>

    <flux:spacer class="mt-4 lg:mt-8" />

    <div>
        <flux:heading class="text-xl">Passwords</flux:heading>

        <flux:spacer class="mt-8" />

        <div class="space-y-6">
            <flux:input :value="$server->sudo_password" label="Sudo password" type="password" readonly copyable viewable />

            <flux:input :value="$server->database_password" label="Database password" type="password" readonly copyable viewable />
        </div>
    </div>
</div>
