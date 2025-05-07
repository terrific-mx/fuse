<?php

use App\Models\Application;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Volt\Component;

new class extends Component {
    public Application $application;
    public ?Collection $deployments;

    public function mount()
    {
        $this->deployments = $this->application->deployments()->get();
    }
}; ?>

<x-layouts.app>
    @volt('pages.applications.index')
        <x-applications.layout :application="$application">
            <section class="space-y-8">
                <div class="flex items-end justify-between gap-4">
                    <flux:heading size="lg" level="2">{{ __('Deployments') }}</flux:heading>

                    <flux:button variant="primary" class="-my-2">
                        {{ __('Deploy') }}
                    </flux:button>
                </div>

                <flux:separator />

                <flux:table class="max-w-lg">
                    <flux:table.columns>
                        <flux:table.column>{{ __('Domain') }}</flux:table.column>
                        <flux:table.column>{{ __('Status') }}</flux:table.column>
                    </flux:table.columns>

                    <flux:table.rows>
                        @foreach ($deployments as $deployment)
                            <flux:table.row>
                                <flux:table.cell variant="strong">
                                    <flux:link href="#">{{ $deployment->created_at }}</flux:link>
                                </flux:table.cell>
                                <flux:table.cell><flux:badge color="green" size="sm" inset="top bottom">{{ $deployment->status }}</flux:badge></flux:table.cell>
                            </flux:table.row>
                        @endforeach
                    </flux:table.rows>
                </flux:table>
            </section>
        </x-applications.layout>
    @endvolt
</x-layouts.app>
