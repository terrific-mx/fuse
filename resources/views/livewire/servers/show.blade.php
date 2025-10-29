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

    <flux:breadcrumbs>
        <flux:breadcrumbs.item :href="route('servers.index')" wire:navigate>Servers</flux:breadcrumbs.item>
    </flux:breadcrumbs>

    <flux:spacer class="mt-8" />

    <flux:heading size="xl">{{ $server->name }}</flux:heading>

    <div class="isolate mt-2.5 flex flex-wrap justify-between gap-x-6 gap-y-4">
        <div class="flex flex-wrap gap-x-10 gap-y-4 py-1.5">
            <flux:text variant="strong" class="flex items-center gap-3" inline>
                <flux:icon.server variant="micro" class="fill-zinc-400 dark:fill-zinc-500" />
                {{ $server->ip_address }}
            </flux:text>
        </div>
        <div class="flex flex-wrap gap-4 -my-1">
            <flux:button.group>
                <flux:button :href="route('servers.sites.index', $server)" wire:navigate>View sites</flux:button>
                <flux:dropdown align="end">
                    <flux:button icon="chevron-down"></flux:button>
                    <flux:menu>
                        <flux:menu.item :href="route('servers.databases.index', $server)" wire:navigate>View databases</flux:menu.item>
                        <flux:menu.separator />
                        <flux:menu.item :href="route('servers.cronjobs.index', $server)" wire:navigate>View cronjobs</flux:menu.item>
                        <flux:menu.separator />
                        <flux:menu.item :href="route('servers.firewall-rules.index', $server)" wire:navigate>View firewall rules</flux:menu.item>
                        <flux:menu.separator />
                        <flux:menu.item :href="route('servers.daemons.index', $server)" wire:navigate>View daemons</flux:menu.item>
                    </flux:menu>
                </flux:dropdown>
            </flux:button.group>
        </div>
    </div>

    <flux:spacer class="mt-8" />

    <x-description.list>
        <x-description.term>
            <flux:text>Ip Address</flux:text>
        </x-description.term>
        <x-description.details>
            <flux:input value="{{ $server->ip_address }}" variant="filled" class="-my-2" readonly copyable />
        </x-description.details>

        <x-description.term>
            <flux:text>Sudo password</flux:text>
        </x-description.term>
        <x-description.details>
            <flux:input value="{{ $server->sudo_password }}" type="password" variant="filled" class="-my-2" readonly viewable copyable />
        </x-description.details>

        <x-description.term>
            <flux:text>Database password</flux:text>
        </x-description.term>
        <x-description.details>
            <flux:input value="{{ $server->database_password }}" type="password" variant="filled" class="-my-2" readonly viewable copyable />
        </x-description.details>
    </x-description.list>
</div>
