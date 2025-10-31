<?php

use App\Models\Server;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Volt\Component;

new #[Title('Server details')] class extends Component
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

<div class="max-w-3xl mx-auto">
    <header class="flex items-center">
        <div class="flex items-center gap-3">
            <flux:avatar :name="strtoupper($server->name)" color="auto" initials:single :color:seed="$server->id" />
            <flux:heading class="text-xl">{{ $server->name }}</flux:heading>
        </div>
        <flux:spacer />
        <flux:dropdown align="end">
            <flux:button icon:trailing="ellipsis-horizontal" size="sm" variant="subtle" />

            <flux:menu>
                <flux:menu.item :href="route('servers.passwords', $server)" wire:navigate>
                    Show passwords
                </flux:menu.item>
            </flux:menu>
        </flux:dropdown>
    </header>

    <flux:text class="mt-4">
        {{ $server->ip_address }}
    </flux:text>

    <flux:spacer class="mt-8" />

    <div class="border-b border-zinc-200 dark:border-zinc-700">
        <flux:navbar class="-mb-px">
            <flux:navbar.item :href="route('servers.sites.index', $server)" :accent="false" wire:navigate>Sites</flux:navbar.item>
            <flux:navbar.item :href="route('servers.databases.index', $server)" :accent="false" wire:navigate>Databases</flux:navbar.item>
            <flux:navbar.item :href="route('servers.cronjobs.index', $server)" :accent="false" wire:navigate>Cronjobs</flux:navbar.item>
            <flux:navbar.item :href="route('servers.firewall-rules.index', $server)" :accent="false" wire:navigate>Firewall rules</flux:navbar.item>
            <flux:navbar.item :href="route('servers.daemons.index', $server)" :accent="false" wire:navigate>Daemons</flux:navbar.item>
        </flux:navbar>
    </div>

    <flux:spacer class="mt-12" />

    <div class="flex items-end justify-between gap-4">
        <flux:heading size="lg">Sites</flux:heading>
        <flux:link :href="route('servers.sites.index', $server)" :accent="false" class="text-sm" wire:navigate>
            See all
        </flux:link>
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
        <flux:callout variant="secondary">No sites have been created for this server yet.</flux:callout>
    @endif

    <flux:spacer class="mt-14" />

    <div class="grid grid-cols-1 gap-x-8 gap-y-14 md:grid-cols-2">
        <div>
            <div class="flex items-end justify-between gap-4">
                <flux:heading size="lg">Databases</flux:heading>
                <flux:link
                    :href="route('servers.databases.index', $server)"
                    :accent="false"
                    class="text-sm"
                    wire:navigate
                >
                    See all
                </flux:link>
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
                <flux:callout variant="secondary">
                    <flux:callout.heading>No databases have been created for this server yet.</flux:callout.heading>
                </flux:callout>
            @endif
        </div>
        <div>
            <div class="flex items-end justify-between gap-4">
                <flux:heading size="lg">Cronjobs</flux:heading>
                <flux:link
                    :href="route('servers.cronjobs.index', $server)"
                    :accent="false"
                    class="text-sm"
                    wire:navigate
                >
                    See all
                </flux:link>
            </div>
            <flux:spacer class="mt-4" />
            @if ($this->cronjobs->count())
                <flux:table>
                    <flux:table.columns>
                        <flux:table.column>Command</flux:table.column>
                    </flux:table.columns>
                    <flux:table.rows>
                        @foreach ($this->cronjobs as $cronjob)
                            <flux:table.row :key="$cronjob->id" class="hover:bg-zinc-950/2.5 dark:hover:bg-white/2.5">
                                <flux:table.cell variant="strong" class="relative max-w-xs truncate">
                                    <a
                                        href="{{ route('servers.cronjobs.edit', ['server' => $server, 'cronjob' => $cronjob]) }}"
                                        class="absolute inset-0"
                                        wire:navigate
                                    ></a>
                                    {{ $cronjob->command }}
                                </flux:table.cell>
                            </flux:table.row>
                        @endforeach
                    </flux:table.rows>
                </flux:table>
            @else
                <flux:callout variant="secondary">
                    <flux:callout.heading>No cronjobs have been created for this server yet.</flux:callout.heading>
                </flux:callout>
            @endif
        </div>
        <div>
            <div class="flex items-end justify-between gap-4">
                <flux:heading size="lg">Firewall Rules</flux:heading>
                <flux:link
                    :href="route('servers.firewall-rules.index', $server)"
                    :accent="false"
                    class="text-sm"
                    wire:navigate
                >
                    See all
                </flux:link>
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
                <flux:callout variant="secondary">
                    <flux:callout.heading>
                        No firewall rules have been created for this server yet.
                    </flux:callout.heading>
                </flux:callout>
            @endif
        </div>
        <div>
            <div class="flex items-end justify-between gap-4">
                <flux:heading size="lg">Daemons</flux:heading>
                <flux:link
                    :href="route('servers.daemons.index', $server)"
                    :accent="false"
                    class="text-sm"
                    wire:navigate
                >
                    See all
                </flux:link>
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
                                <flux:table.cell variant="strong" class="max-w-xs truncate">
                                    {{ $daemon->command }}
                                </flux:table.cell>
                            </flux:table.row>
                        @endforeach
                    </flux:table.rows>
                </flux:table>
            @else
                <flux:callout variant="secondary">
                    <flux:callout.heading>No daemons have been created for this server yet.</flux:callout.heading>
                </flux:callout>
            @endif
        </div>
    </div>
</div>
