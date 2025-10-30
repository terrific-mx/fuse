<?php

use App\Models\Server;
use Livewire\Attributes\Computed;
use Livewire\Volt\Component;

new class extends Component
{
    public Server $server;

    #[Computed]
    public function sites()
    {
        return $this->server->sites()->latest()->limit(3)->get();
    }

    #[Computed]
    public function databases()
    {
        return $this->server->databases()->latest()->limit(3)->get();
    }

    #[Computed]
    public function cronjobs()
    {
        return $this->server->cronjobs()->latest()->limit(3)->get();
    }

    #[Computed]
    public function firewallRules()
    {
        return $this->server->firewallRules()->latest()->limit(3)->get();
    }

    #[Computed]
    public function daemons()
    {
        return $this->server->daemons()->latest()->limit(3)->get();
    }

    public function mount()
    {
        $this->authorize('view', $this->server);
    }
}; ?>

<div>
    <flux:heading size="xl">{{ $server->name }}</flux:heading>

    <div class="isolate mt-2.5 flex flex-wrap items-end justify-between gap-x-6 gap-y-4">
        <div class="flex flex-wrap gap-x-10 gap-y-4">
            <flux:text variant="strong" class="flex items-center gap-3" inline>
                <flux:icon.server variant="micro" class="fill-zinc-400 dark:fill-zinc-500" />
                {{ $server->ip_address }}
            </flux:text>
        </div>
        <div class="flex flex-wrap gap-4">
            <flux:dropdown align="end">
                <flux:button icon:trailing="chevron-down">Actions</flux:button>

                <flux:menu>
                    <flux:menu.item :href="route('servers.passwords', $server)" wire:navigate>Show passwords</flux:menu.item>
                </flux:menu>
            </flux:menu>
        </div>
    </div>

    <flux:spacer class="mt-8" />

    <div class="flex items-end justify-between gap-4">
        <flux:heading size="lg">Sites</flux:heading>
        <flux:button :href="route('servers.sites.index', $server)" variant="primary" color="zinc" class="-my-2" wire:navigate>
            View
        </flux:button>
    </div>

    <flux:spacer class="mt-4" />

    @if ($this->sites->count())
        <flux:table>
            <flux:table.columns>
                <flux:table.column>Hostname</flux:table.column>
                <flux:table.column>Repository</flux:table.column>
            </flux:table.columns>
            <flux:table.rows>
                @foreach ($this->sites as $site)
                    <flux:table.row :key="$site->id" class="hover:bg-zinc-950/2.5 dark:hover:bg-white/2.5">
                        <flux:table.cell variant="strong" class="relative">
                            <a
                                href="{{ route('servers.sites.show', ['server' => $server, 'site' => $site]) }}"
                                class="absolute inset-0"
                                wire:navigate
                            ></a>
                            {{ $site->hostname }}
                        </flux:table.cell>
                        <flux:table.cell class="relative">
                            <a
                                href="{{ route('servers.sites.show', ['server' => $server, 'site' => $site]) }}"
                                class="absolute inset-0"
                                wire:navigate
                            ></a>
                            {{ $site->repository_url }}
                        </flux:table.cell>
                    </flux:table.row>
                @endforeach
            </flux:table.rows>
        </flux:table>
    @else
        <flux:callout variant="secondary" class="mt-2">No sites have been created for this server yet.</flux:callout>
    @endif

    <flux:spacer class="mt-14" />

    <div class="grid grid-cols-1 gap-x-8 gap-y-14 md:grid-cols-2">
        <div>
            <div class="flex items-end justify-between gap-4">
                <flux:heading size="lg">Databases</flux:heading>
                <flux:button
                    :href="route('servers.databases.index', $server)"
                    variant="primary"
                    color="zinc"
                    class="-my-2"
                    wire:navigate
                >
                    View
                </flux:button>
            </div>
            <flux:spacer class="mt-4" />
            @if ($this->databases->count())
                <flux:table>
                    <flux:table.columns>
                        <flux:table.column>Name</flux:table.column>
                    </flux:table.columns>
                    <flux:table.rows>
                        @foreach ($this->databases as $database)
                            <flux:table.row :key="$database->id">
                                <flux:table.cell variant="strong" class="relative">
                                    {{ $database->name }}
                                </flux:table.cell>
                            </flux:table.row>
                        @endforeach
                    </flux:table.rows>
                </flux:table>
            @else
                <flux:callout variant="secondary" class="mt-2">
                    <flux:callout.heading>No databases have been created for this server yet.</flux:callout.heading>
                </flux:callout>
            @endif
        </div>
        <div>
            <div class="flex items-end justify-between gap-4">
                <flux:heading size="lg">Cronjobs</flux:heading>
                <flux:button
                    :href="route('servers.cronjobs.index', $server)"
                    variant="primary"
                    color="zinc"
                    class="-my-2"
                    wire:navigate
                >
                    View
                </flux:button>
            </div>
            <flux:spacer class="mt-4" />
            @if ($this->cronjobs->count())
                <flux:table>
                    <flux:table.columns>
                        <flux:table.column>Command</flux:table.column>
                    </flux:table.columns>
                    <flux:table.rows>
                        @foreach ($this->cronjobs as $cronjob)
                            <flux:table.row :key="$cronjob->id">
                                <flux:table.cell class="max-w-xs truncate">{{ $cronjob->command }}</flux:table.cell>
                            </flux:table.row>
                        @endforeach
                    </flux:table.rows>
                </flux:table>
            @else
                <flux:callout variant="secondary" class="mt-2">
                    <flux:callout.heading>No cronjobs have been created for this server yet.</flux:callout.heading>
                </flux:callout>
            @endif
        </div>
        <div>
            <div class="flex items-end justify-between gap-4">
                <flux:heading size="lg">Firewall Rules</flux:heading>
                <flux:button
                    :href="route('servers.firewall-rules.index', $server)"
                    variant="primary"
                    color="zinc"
                    class="-my-2"
                    wire:navigate
                >
                    View
                </flux:button>
            </div>
            <flux:spacer class="mt-4" />
            @if ($this->firewallRules->count())
                <flux:table>
                    <flux:table.columns>
                        <flux:table.column>Name</flux:table.column>
                    </flux:table.columns>
                    <flux:table.rows>
                        @foreach ($this->firewallRules as $rule)
                            <flux:table.row :key="$rule->id">
                                <flux:table.cell variant="strong">
                                    {{ $rule->name ?? ucfirst($rule->type) }}
                                </flux:table.cell>
                            </flux:table.row>
                        @endforeach
                    </flux:table.rows>
                </flux:table>
            @else
                <flux:callout variant="secondary" class="mt-2">
                    <flux:callout.heading>
                        No firewall rules have been created for this server yet.
                    </flux:callout.heading>
                </flux:callout>
            @endif
        </div>
        <div>
            <div class="flex items-end justify-between gap-4">
                <flux:heading size="lg">Daemons</flux:heading>
                <flux:button
                    :href="route('servers.daemons.index', $server)"
                    variant="primary"
                    color="zinc"
                    class="-my-2"
                    wire:navigate
                >
                    View
                </flux:button>
            </div>
            <flux:spacer class="mt-4" />
            @if ($this->daemons->count())
                <flux:table>
                    <flux:table.columns>
                        <flux:table.column>Command</flux:table.column>
                    </flux:table.columns>
                    <flux:table.rows>
                        @foreach ($this->daemons as $daemon)
                            <flux:table.row :key="$daemon->id">
                                <flux:table.cell class="max-w-xs truncate">{{ $daemon->command }}</flux:table.cell>
                            </flux:table.row>
                        @endforeach
                    </flux:table.rows>
                </flux:table>
            @else
                <flux:callout variant="secondary" class="mt-2">
                    <flux:callout.heading>No daemons have been created for this server yet.</flux:callout.heading>
                </flux:callout>
            @endif
        </div>
    </div>
</div>
