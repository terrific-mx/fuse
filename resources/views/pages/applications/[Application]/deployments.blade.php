<?php

use App\Jobs\DeployApplication;
use App\Models\Application;
use Illuminate\Database\Eloquent\Collection;
use Laravel\WorkOS\Http\Middleware\ValidateSessionWithWorkOS;
use Livewire\Volt\Component;

use function Laravel\Folio\middleware;

middleware(['auth', 'can:view,application', ValidateSessionWithWorkOS::class]);

new class extends Component {
    public Application $application;
    public ?Collection $deployments;

    public function mount()
    {
        $this->fetchDeployments();
    }

    public function deploy()
    {
        if ($this->application->deployments()->pending()->exists()) {
            return;
        }

        $deployment = $this->application->deployments()->create(['status' => 'pending']);

        DeployApplication::dispatch($this->application->server, $this->application, $deployment);

        $this->fetchDeployments();
    }

    protected function fetchDeployments()
    {
        $this->deployments = $this->application->deployments()->latest()->get();
    }
}; ?>
<x-layouts.app>
    @volt('pages.applications.deployments')
        <x-applications.layout :application="$application">
            <section class="space-y-8">
                <div class="flex items-end justify-between gap-4">
                    <flux:heading size="lg" level="2">{{ __('Deployments') }}</flux:heading>

                    <form wire:submit="deploy">
                        <flux:button type="submit" variant="primary" class="-my-2">
                            {{ __('Deploy') }}
                        </flux:button>
                    </form>
                </div>

                <flux:separator />

                <flux:table class="max-w-lg" wire:poll>
                    <flux:table.columns>
                        <flux:table.column>{{ __('Deployment') }}</flux:table.column>
                        <flux:table.column>{{ __('Status') }}</flux:table.column>
                    </flux:table.columns>

                    <flux:table.rows>
                        @foreach ($deployments as $deployment)
                            <flux:table.row>
                                <flux:table.cell variant="strong" class="tabular-nums">
                                    {{ $deployment->created_at }}
                                </flux:table.cell>
                                <flux:table.cell>
                                    <flux:badge
                                        :icon="$deployment->status === 'pending' ? 'loading' : ''"
                                        :color="$deployment->status === 'finished' ? 'green' : 'amber'"
                                        size="sm"
                                        inset="top bottom"
                                    >{{ $deployment->status }}</flux:badge>
                                </flux:table.cell>
                            </flux:table.row>
                        @endforeach
                    </flux:table.rows>
                </flux:table>
            </section>
        </x-applications.layout>
    @endvolt
</x-layouts.app>
