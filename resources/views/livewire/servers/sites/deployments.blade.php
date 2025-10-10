<?php

use App\Jobs\DeploySite;
use App\Models\Deployment;
use App\Models\Server;
use App\Models\Site;
use Flux\Flux;
use Livewire\Attributes\Computed;
use Livewire\Volt\Component;

use Livewire\WithPagination;

new class extends Component {
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

<x-slot:breadcrumbs>
    @include('partials.site-breadcrumbs', ['server' => $server, 'site' => $site, 'current' => __('Deployments')])
</x-slot:breadcrumbs>

<div>
    @include('partials.site-heading')
    <div class="flex items-start max-md:flex-col">
        @include('partials.site-navbar', ['server' => $server, 'site' => $site])
        <flux:separator class="md:hidden" />
        <div class="flex-1 self-stretch max-md:pt-6">
            <section>
                <header class="flex flex-wrap items-end justify-between gap-4">
                    <div class="max-sm:w-full sm:flex-1">
                        <flux:heading>{{ __('Deployments') }}</flux:heading>
                        <flux:text class="mt-2 max-w-prose">{{ __('View and manage deployments for this site.') }}</flux:text>
                    </div>
                    <flux:button wire:click="triggerDeployment" variant="primary">{{ __('Deploy') }}</flux:button>
                </header>

                <div class="mt-4">
                    <flux:table :paginate="$this->deployments" wire:poll>
                        <flux:table.columns>
                            <flux:table.column>{{ __('Deployed At') }}</flux:table.column>
                            <flux:table.column>{{ __('Triggered By') }}</flux:table.column>
                            <flux:table.column>{{ __('Commit') }}</flux:table.column>
                            <flux:table.column>{{ __('Status') }}</flux:table.column>
                        </flux:table.columns>
                        <flux:table.rows>
                            @foreach($this->deployments as $deployment)
                                <flux:table.row :key="$deployment->id">
                                    <flux:table.cell variant="strong">{{ $deployment->created_at?->format('Y-m-d H:i') }}</flux:table.cell>
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
        </div>
    </div>

    <flux:modal name="showDeploymentModal" variant="flyout" class="max-w-2xl">
        @if($selectedDeployment)
            <div class="space-y-6">
                <div>
                    <flux:heading size="lg">
                        {{ $selectedDeployment->created_at->format('Y-m-d H:i') }}
                    </flux:heading>
                    <flux:text class="mt-2">
                        {{ __('Below is the log output for this deployment.') }}
                    </flux:text>
                </div>
                <div>
                    <flux:field>
                        <flux:label>{{ __('Log Output') }}</flux:label>
                        <flux:text>
                            <pre id="deployment-log-output" class="bg-zinc-100 dark:bg-zinc-800 rounded p-3 overflow-x-auto text-xs font-mono" tabindex="0">{{ $selectedDeployment->log ?? __('No log output available.') }}</pre>
                        </flux:text>
                    </flux:field>
                </div>
            </div>
        @endif
    </flux:modal>
</div>
