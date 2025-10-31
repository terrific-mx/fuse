<?php

use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Volt\Component;
use Livewire\WithPagination;

new #[Title('Home')] class extends Component
{
    use WithPagination;

    #[Computed]
    public function organization()
    {
        return Auth::user()->currentOrganization;
    }

    #[Computed]
    public function servers()
    {
        return $this->organization->servers()
            ->orderByDesc('created_at')
            ->limit(3)
            ->get();
    }

    #[Computed]
    public function sites()
    {
        return $this->organization->sites()
            ->with('server')
            ->orderByDesc('created_at')
            ->limit(3)
            ->get();
    }
}; ?>

<div class="max-w-3xl mx-auto" wire:poll>
    <header class="flex items-center">
        <div class="flex items-center gap-3">
            <flux:avatar :name="$this->organization->name" color="auto" initials:single :color:seed="$this->organization->name" />
            <flux:heading class="text-xl">{{ $this->organization->name}}</flux:heading>
        </div>
        <flux:spacer />
        <flux:dropdown align="end">
            <flux:button icon:trailing="ellipsis-horizontal" size="sm" variant="subtle" />

            <flux:menu>
                <flux:menu.item :href="route('organizations.settings.general', $this->organization)" icon="cog-8-tooth" icon:variant="micro" wire:navigate>
                    Settings
                </flux:menu.item>
            </flux:menu>
        </flux:dropdown>
    </header>

    <flux:spacer class="mt-5" />

    <header class="flex items-center">
        <flux:heading size="lg">Recent servers</flux:heading>
        <flux:spacer />
        <flux:button :href="route('servers.create')" variant="primary" color="zinc" size="sm" icon="plus" class="-my-1" wire:navigate>
            New server
        </flux:button>
    </header>

    <flux:separator class="mt-3" />

    <flux:table>
        <flux:table.rows>
            @foreach ($this->servers as $server)
                <flux:table.row :key="$server->id">
                    <flux:table.cell class="w-full">
                        <div class="flex items-center gap-3">
                            <flux:avatar :name="strtoupper($server->name)" size="xs" color="auto" initials:single :color:seed="$server->id" />
                            <flux:link :href="route('servers.show', $server)" :accent="false" wire:navigate>{{ $server->name }}</flux:link>
                        </div>
                    </flux:table.cell>
                    <flux:table.cell align="end">
                        <flux:badge
                            :color="$server->status_color"
                            size="sm"
                            inset="top bottom"
                            @class(['animate-pulse' => $server->is_provisioning])
                        >
                            {{ $server->status_formatted }}
                        </flux:badge>
                    </flux:table.cell>
                    <flux:table.cell align="end" class="text-xs">
                        {{ $server->created_at->format('M d') }}
                    </flux:table.cell>
                </flux:table.row>
            @endforeach
        </flux:table.rows>
    </flux:table>

    <flux:spacer class="mt-7" />

    <flux:heading size="lg">Recent sites</flux:heading>

    <flux:separator class="mt-3" />

    <flux:table>
        <flux:table.rows>
            @foreach ($this->sites as $site)
                <flux:table.row :key="$site->id">
                    <flux:table.cell class="w-full">
                        <div class="flex items-center gap-3">
                            <flux:avatar :name="strtoupper($site->hostname)" size="xs" color="auto" initials:single :color:seed="$site->id" />
                            <flux:link :href="route('servers.sites.show', [$site->server, $site])" :accent="false" wire:navigate>{{ $site->hostname }}</flux:link>
                        </div>
                    </flux:table.cell>
                    <flux:table.cell align="end">
                        {{ $site->server->name }}
                    </flux:table.cell>
                    <flux:table.cell align="end" class="text-xs">
                        {{ $site->created_at->format('M d') }}
                    </flux:table.cell>
                </flux:table.row>
            @endforeach
        </flux:table.rows>
    </flux:table>
</div>
