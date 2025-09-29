<?php

use App\Models\Server;
use App\Models\Site;
use Livewire\Volt\Component;

new class extends Component {
    public Server $server;
    public Site $site;

    public function triggerDeployment(): void
    {
        $this->site->deployments()->create([
            'status' => 'pending',
            'triggered_by' => $this->server->organization->user->id,
        ]);
    }
}; ?>

<div class="space-y-12">
    @include('partials.site-navbar', ['server' => $server, 'site' => $site])

    <section class="space-y-6">
        <flux:heading size="lg">{{ __('Deployments') }}</flux:heading>

        <flux:button variant="primary" class="mb-4">{{ __('Dispatch Deployment') }}</flux:button>

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
                <flux:table.row :key="1">
                    <flux:table.cell>#1</flux:table.cell>
                    <flux:table.cell><flux:badge color="green" size="sm" inset="top bottom">{{ __('Success') }}</flux:badge></flux:table.cell>
                    <flux:table.cell>c0ffee1</flux:table.cell>
                    <flux:table.cell>2025-09-27 18:45</flux:table.cell>
                    <flux:table.cell>oliver</flux:table.cell>
                    <flux:table.cell><flux:button size="sm">{{ __('View') }}</flux:button></flux:table.cell>
                </flux:table.row>
                <flux:table.row :key="2">
                    <flux:table.cell>#2</flux:table.cell>
                    <flux:table.cell><flux:badge color="red" size="sm" inset="top bottom">{{ __('Failed') }}</flux:badge></flux:table.cell>
                    <flux:table.cell>deadbeef</flux:table.cell>
                    <flux:table.cell>2025-09-26 14:22</flux:table.cell>
                    <flux:table.cell>oliver</flux:table.cell>
                    <flux:table.cell><flux:button size="sm">{{ __('View') }}</flux:button></flux:table.cell>
                </flux:table.row>
                <flux:table.row :key="3">
                    <flux:table.cell>#3</flux:table.cell>
                    <flux:table.cell><flux:badge color="amber" size="sm" inset="top bottom">{{ __('Pending') }}</flux:badge></flux:table.cell>
                    <flux:table.cell>abc1234</flux:table.cell>
                    <flux:table.cell>2025-09-27 23:00</flux:table.cell>
                    <flux:table.cell>oliver</flux:table.cell>
                    <flux:table.cell><flux:button size="sm">{{ __('View') }}</flux:button></flux:table.cell>
                </flux:table.row>
            </flux:table.rows>
        </flux:table>

    </section>
</div>
