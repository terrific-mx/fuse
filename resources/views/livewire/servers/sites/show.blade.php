<?php

use App\Jobs\DeploySite;
use App\Models\Server;
use App\Models\Site;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Volt\Component;

new #[Title('Site details')] class extends Component
{
    public Server $server;

    public Site $site;

    public function mount()
    {
        $this->authorize('view', $this->site);
    }

    #[Computed]
    public function deployments()
    {
        return $this->site->deployments()
            ->with('triggeredBy')
            ->orderByDesc('created_at')
            ->limit(10)
            ->get();
    }

    public function triggerDeployment(): void
    {
        $deployment = $this->site->deployments()->create([
            'status' => 'pending',
            'triggered_by' => $this->site->server->organization->user->id,
        ]);

        DeploySite::dispatch($deployment);
    }
}; ?>

<x-layouts.site :site="$site" :server="$server">
    <header class="flex items-center">
        <flux:heading size="lg">Latest deployments</flux:heading>
        <flux:spacer />
        <flux:button wire:click="triggerDeployment" variant="primary" color="zinc" size="sm" icon="cloud-arrow-up" class="-my-1">
            Deploy
        </flux:button>
    </header>

    <flux:separator class="mt-3" />

    <flux:table wire:poll>
        <flux:table.rows>
            @foreach ($this->deployments as $deployment)
                <flux:table.row :key="$deployment->id">
                    <flux:table.cell variant="strong" class="w-full">
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
                    <flux:table.cell align="end">
                        {{ $deployment->created_at->diffForHumans() }}
                    </flux:table.cell>
                </flux:table.row>
            @endforeach
        </flux:table.rows>
    </flux:table>
</x-layouts.site>
