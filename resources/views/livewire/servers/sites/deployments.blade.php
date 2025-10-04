<?php

use App\Jobs\DeploySite;
use App\Models\Server;
use App\Models\Site;
use Livewire\Attributes\Computed;
use Livewire\Volt\Component;

new class extends Component {
    public Server $server;

    public Site $site;

    public function mount()
    {
        $this->authorize('view', $this->server);

        $this->authorize('view', $this->site);
    }

    #[Computed]
    public function deployments()
    {
        return $this->site->deployments()->latest()->get();
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
                    <flux:table>
                        <flux:table.columns>
                            <flux:table.column>{{ __('Deployment number') }}</flux:table.column>
                            <flux:table.column>{{ __('Status') }}</flux:table.column>
                            <flux:table.column>{{ __('Commit') }}</flux:table.column>
                            <flux:table.column>{{ __('Deployed At') }}</flux:table.column>
                            <flux:table.column align="end">{{ __('Triggered By') }}</flux:table.column>
                        </flux:table.columns>
                        <flux:table.rows>
                            @foreach($this->deployments as $deployment)
                                <flux:table.row :key="$deployment->id">
                                    <flux:table.cell>#{{ $deployment->id }}</flux:table.cell>
                                    <flux:table.cell>
                                        <flux:badge
                                            :color="$deployment->status === 'success' ? 'green' : ($deployment->status === 'failed' ? 'red' : 'amber')"
                                            size="sm"
                                            inset="top bottom"
                                        >{{ __(ucfirst($deployment->status)) }}</flux:badge>
                                    </flux:table.cell>
                                    <flux:table.cell>{{ $deployment->commit ?? '-' }}</flux:table.cell>
                                    <flux:table.cell>{{ $deployment->deployed_at ? $deployment->deployed_at->format('Y-m-d H:i') : '-' }}</flux:table.cell>
                                    <flux:table.cell align="end">{{ $deployment->triggered_by ? \App\Models\User::find($deployment->triggered_by)?->name ?? '-' : '-' }}</flux:table.cell>
                                </flux:table.row>
                            @endforeach
                        </flux:table.rows>
                    </flux:table>
                </div>
            </section>
        </div>
    </div>
</div>
