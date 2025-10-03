<?php

use App\Jobs\DeploySite;
use App\Models\Server;
use App\Models\Site;
use Livewire\Attributes\Computed;
use Livewire\Volt\Component;

new class extends Component {
    public Server $server;
    public Site $site;

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

<div class="space-y-12">
    @include('partials.site-navbar', ['server' => $server, 'site' => $site])

    <section class="space-y-6">
        <flux:heading size="lg">{{ __('Deployments') }}</flux:heading>

        <flux:button wire:click="triggerDeployment" variant="primary" class="mb-4">{{ __('Dispatch Deployment') }}</flux:button>

        <flux:table>
            <flux:table.columns>
                <flux:table.column>{{ __('ID') }}</flux:table.column>
                <flux:table.column>{{ __('Status') }}</flux:table.column>
                <flux:table.column>{{ __('Commit') }}</flux:table.column>
                <flux:table.column>{{ __('Deployed At') }}</flux:table.column>
                <flux:table.column>{{ __('Triggered By') }}</flux:table.column>
                <flux:table.column>{{ __('Actions') }}</flux:table.column>
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
                        <flux:table.cell>{{ $deployment->triggered_by ? \App\Models\User::find($deployment->triggered_by)?->name ?? '-' : '-' }}</flux:table.cell>
                        <flux:table.cell><flux:button size="sm">{{ __('View') }}</flux:button></flux:table.cell>
                    </flux:table.row>
                @endforeach
            </flux:table.rows>
        </flux:table>
    </section>
</div>
