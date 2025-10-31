<?php

use App\Models\Server;
use App\Models\Site;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Volt\Component;

new #[Title('Site details')] class extends Component
{
    public function triggerDeployment(): void
    {
        $deployment = $this->site->deployments()->create([
            'status' => 'pending',
            'triggered_by' => $this->site->server->organization->user->id,
        ]);
        \App\Jobs\DeploySite::dispatch($deployment);
    }

    public Server $server;

    public Site $site;

    #[Computed]
    public function deployments()
    {
        return $this->site->deployments()
            ->with('triggeredBy')
            ->orderByDesc('created_at')
            ->limit(10)
            ->get();
    }

    public function mount()
    {
        $this->authorize('view', $this->site);
    }
}; ?>

<div wire:poll>
    <flux:breadcrumbs>
        <flux:breadcrumbs.item :href="route('servers.index')" wire:navigate>Servers</flux:breadcrumbs.item>
        <flux:breadcrumbs.item :href="route('servers.show', $server)" wire:navigate>
            {{ $server->name }}
        </flux:breadcrumbs.item>
        <flux:breadcrumbs.item :href="route('servers.sites.index', $server)" wire:navigate>Sites</flux:breadcrumbs.item>
    </flux:breadcrumbs>

    <flux:spacer class="mt-8" />

    <flux:heading size="xl">{{ $site->hostname }}</flux:heading>

    <div class="isolate mt-2.5 flex flex-wrap items-center justify-between gap-x-6 gap-y-4">
        <div class="flex flex-wrap gap-x-10 gap-y-4 py-1.5">
            <flux:text variant="strong" class="flex items-center gap-3" inline>
                <flux:icon.server variant="micro" class="fill-zinc-400 dark:fill-zinc-500" />
                {{ $site->repository_url }}
            </flux:text>
            <flux:text variant="strong" class="flex items-center gap-3" inline>
                <flux:icon.server variant="micro" class="fill-zinc-400 dark:fill-zinc-500" />
                {{ $site->repository_branch }}
            </flux:text>
            <flux:text variant="strong" class="flex items-center gap-3" inline>
                <flux:icon.server variant="micro" class="fill-zinc-400 dark:fill-zinc-500" />
                PHP {{ $site->php_version }}
            </flux:text>
        </div>
        <div class="flex flex-wrap gap-4">
            <flux:dropdown align="end">
                <flux:button icon:trailing="chevron-down">Actions</flux:button>
                <flux:menu>
                    <flux:menu.item :href="route('servers.sites.files', [$server, $site])" wire:navigate>
                        Edit files
                    </flux:menu.item>
                    <flux:menu.item :href="route('servers.sites.deployment-settings', [$server, $site])" wire:navigate>
                        Edit deployment settings
                    </flux:menu.item>
                </flux:menu>
            </flux:dropdown>

            <flux:button wire:click="triggerDeployment" variant="primary" color="zinc">Deploy</flux:button>
        </div>
    </div>

    <flux:spacer class="mt-12" />

    <div class="flex items-end justify-between gap-4">
        <flux:heading size="lg">Deployments</flux:heading>
        <flux:link
            :href="route('servers.sites.deployments', [$server, $site])"
            :accent="false"
            class="text-sm"
            wire:navigate
        >
            See all
        </flux:link>
    </div>

    <flux:spacer class="mt-4" />

    <flux:table wire:poll>
        <flux:table.columns>
            <flux:table.column>Commit</flux:table.column>
            <flux:table.column>Status</flux:table.column>
            <flux:table.column>Triggered By</flux:table.column>
            <flux:table.column>Created At</flux:table.column>
        </flux:table.columns>
        <flux:table.rows>
            @foreach ($this->deployments as $deployment)
                <flux:table.row :key="$deployment->id">
                    <flux:table.cell>
                        {{ $deployment->short_commit ?? '—' }}
                    </flux:table.cell>
                    <flux:table.cell>
                        <flux:badge :color="$deployment->status_color" size="sm">
                            {{ $deployment->status_formatted }}
                        </flux:badge>
                    </flux:table.cell>
                    <flux:table.cell>
                        {{ $deployment->triggeredBy?->name ?? '—' }}
                    </flux:table.cell>
                    <flux:table.cell>
                        {{ $deployment->created_at->format('Y-m-d H:i') }}
                    </flux:table.cell>
                </flux:table.row>
            @endforeach
        </flux:table.rows>
    </flux:table>
</div>
