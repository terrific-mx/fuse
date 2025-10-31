<?php

use App\Jobs\DeploySite;
use App\Models\Deployment;
use App\Models\Server;
use App\Models\Site;
use Flux\Flux;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Volt\Component;
use Livewire\WithPagination;

new #[Title('Deployments')] class extends Component
{
    use WithPagination;

    public Server $server;

    public Site $site;

    public ?Deployment $selectedDeployment = null;

    public function mount()
    {
        $this->authorize('view', $this->server);
        $this->authorize('view', $this->site);
    }

    #[Computed]
    public function deployments()
    {
        return $this->site->deployments()->latest()->paginate(10);
    }

    public function showDeployment(Deployment $deployment)
    {
        $this->authorize('view', $deployment);

        $this->selectedDeployment = $deployment;

        Flux::modal('showDeploymentModal')->show();
    }

    public function triggerDeployment(): void
    {
        $deployment = $this->site->deployments()->create([
            'status' => 'pending',
            'triggered_by' => $this->server->organization->user->id,
        ]);

        DeploySite::dispatch($deployment);
    }
}; ?>

<x-layouts.site :site="$site" :server="$server">
    <header class="flex items-center">
        <flux:heading size="lg">Deployments</flux:heading>
        <flux:spacer />
        <flux:button wire:click="triggerDeployment" variant="primary" color="zinc" size="sm" icon="cloud-arrow-up" class="-my-1">
            Deploy
        </flux:button>
    </header>

    <flux:separator class="mt-3" />

    <flux:table :paginate="$this->deployments" wire:poll>
        <flux:table.rows>
            @foreach ($this->deployments as $deployment)
                <flux:table.row :key="$deployment->id">
    <flux:table.cell variant="strong" class="tabular-nums">
        {{ $deployment->short_commit ?? '-' }}
    </flux:table.cell>
    <flux:table.cell>
        {{ $deployment->triggered_by ? \App\Models\User::find($deployment->triggered_by)?->name ?? '-' : '-' }}
    </flux:table.cell>
    <flux:table.cell>
        <flux:badge
            :color="$deployment->status_color"
            size="sm"
            inset="top bottom"
            @class(['animate-pulse' => $deployment->is_pending || $deployment->isDeploying()])
        >
            {{ $deployment->status_formatted }}
        </flux:badge>
    </flux:table.cell>
    <flux:table.cell>
        {{ $deployment->created_at?->diffForHumans() }}
    </flux:table.cell>
    <flux:table.cell align="end">
        <flux:button
            wire:click="showDeployment({{ $deployment->id }})"
            variant="ghost"
            size="sm"
            icon="ellipsis-horizontal"
            inset="top bottom"
        ></flux:button>
    </flux:table.cell>
</flux:table.row>
            @endforeach
        </flux:table.rows>
    </flux:table>

    <flux:modal name="showDeploymentModal" variant="flyout" class="max-w-2xl">
        @if ($selectedDeployment)
            <div class="space-y-6">
                <div>
                    <flux:heading size="lg">
                        {{ $selectedDeployment->created_at->diffForHumans() }}
                    </flux:heading>
                    <flux:text class="mt-2">Below is the log output for this deployment.</flux:text>
                </div>
                <div>
                    <flux:field>
                        <flux:label>Log Output</flux:label>
                        <flux:text>
                            <pre class="overflow-x-auto rounded bg-zinc-100 p-3 font-mono text-xs dark:bg-zinc-800">
{{ $selectedDeployment->output ?? __('No log output available.') }}</pre
                            >
                        </flux:text>
                    </flux:field>
                </div>
            </div>
        @endif
    </flux:modal>
</x-layouts.site>
