<?php

use App\Jobs\DeploySite;
use App\Models\Deployment;
use App\Models\Server;
use App\Models\Site;
use Flux\Flux;
use Livewire\Attributes\Computed;
use Livewire\Volt\Component;
use Livewire\WithPagination;

new class extends Component
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

<div>
    <header>
        <flux:breadcrumbs class="mb-2">
            <flux:breadcrumbs.item :href="route('servers.show', $server)" separator="slash" wire:navigate>{{ $server->name }}</flux:breadcrumbs.item>
        </flux:breadcrumbs>
        <flux:heading size="xl">{{ $site->hostname }}</flux:heading>
        @include('partials.site-navbar')
    </header>
    <section class="mt-8">
        <flux:button wire:click="triggerDeployment" variant="primary">Deploy</flux:button>

        <div class="mt-4">
            <flux:table :paginate="$this->deployments" wire:poll>
                <flux:table.columns>
                    <flux:table.column>Deployed At</flux:table.column>
                    <flux:table.column>Triggered By</flux:table.column>
                    <flux:table.column>Commit</flux:table.column>
                    <flux:table.column>Status</flux:table.column>
                </flux:table.columns>
                <flux:table.rows>
                    @foreach($this->deployments as $deployment)
                        <flux:table.row :key="$deployment->id">
                            <flux:table.cell variant="strong" class="tabular-nums">{{ $deployment->created_at?->format('Y-m-d H:i') }}</flux:table.cell>
                            <flux:table.cell>{{ $deployment->triggered_by ? \App\Models\User::find($deployment->triggered_by)?->name ?? '-' : '-' }}</flux:table.cell>
                            <flux:table.cell>{{ $deployment->short_commit ?? '-' }}</flux:table.cell>
                            <flux:table.cell>
                                <flux:badge
                                    :color="$deployment->status_color"
                                    size="sm"
                                    inset="top bottom"
                                    @class(['animate-pulse' => $deployment->is_pending || $deployment->isDeploying()])
                                >{{ $deployment->status_formatted }}</flux:badge>
                            </flux:table.cell>
                            <flux:table.cell align="end">
                                <flux:button wire:click="showDeployment({{ $deployment->id }})" variant="ghost" size="sm" icon="ellipsis-horizontal" inset="top bottom"></flux:button>
                            </flux:table.cell>
                        </flux:table.row>
                    @endforeach
                </flux:table.rows>
            </flux:table>
        </div>
    </section>

    <flux:modal name="showDeploymentModal" variant="flyout" class="max-w-2xl">
        @if($selectedDeployment)
            <div class="space-y-6">
                <div>
                    <flux:heading size="lg">
                        {{ $selectedDeployment->created_at->format('Y-m-d H:i') }}
                    </flux:heading>
                    <flux:text class="mt-2">
                        Below is the log output for this deployment.
                    </flux:text>
                </div>
                <div>
                    <flux:field>
                        <flux:label>Log Output</flux:label>
                        <flux:text>
                            <pre class="bg-zinc-100 dark:bg-zinc-800 rounded p-3 overflow-x-auto text-xs font-mono">{{ $selectedDeployment->output ?? __('No log output available.') }}</pre>
                        </flux:text>
                    </flux:field>
                </div>
            </div>
        @endif
    </flux:modal>
</div>
