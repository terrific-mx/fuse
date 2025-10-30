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

<div>
    <flux:breadcrumbs>
        <flux:breadcrumbs.item :href="route('servers.index')" wire:navigate>Servers</flux:breadcrumbs.item>
        <flux:breadcrumbs.item :href="route('servers.show', $server)" wire:navigate>
            {{ $server->name }}
        </flux:breadcrumbs.item>
    </flux:breadcrumbs>

    <flux:spacer class="mt-8" />

    <div>
        <flux:heading size="xl">Passwords</flux:heading>

        <flux:separator class="my-10 mt-6" />

        <section class="grid gap-x-8 gap-y-6 sm:grid-cols-2">
            <div>
                <flux:heading>Sudo password</flux:heading>
            </div>
            <div>
                <flux:input :value="$server->sudo_password" type="password" readonly copyable viewable />
            </div>
        </section>

        <flux:separator variant="subtle" class="my-10" />

        <section class="grid gap-x-8 gap-y-6 sm:grid-cols-2">
            <div>
                <flux:heading>Database password</flux:heading>
            </div>
            <div>
                <flux:input :value="$server->database_password" type="password" readonly copyable viewable />
            </div>
        </section>
    </div>
</div>
