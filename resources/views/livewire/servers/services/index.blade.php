<?php

use App\Models\Server;
use App\Models\Service;
use Livewire\Attributes\Computed;
use Livewire\Volt\Component;

new class extends Component
{
    public Server $server;

    public function mount()
    {
        $this->authorize('view', $this->server);
    }

    #[Computed]
    public function services()
    {
        return Service::all();
    }
}; ?>

<div class="space-y-8">
    <header class="-mt-6 flex items-center lg:-mt-8">
        <flux:heading size="lg">{{ $server->name }}</flux:heading>
        <flux:spacer />
        @include('partials.server-navbar')
    </header>

    <section class="mt-12">
        <div class="flex items-center justify-between">
            <flux:heading size="xl">Services</flux:heading>
        </div>

        <div class="mt-4">
            <flux:table>
                <flux:table.columns>
                    <flux:table.column>Name</flux:table.column>
                    <flux:table.column></flux:table.column>
                </flux:table.columns>

                <flux:table.rows>
                    @foreach ($this->services as $service)
                        <flux:table.row :key="$service->id">
                            <flux:table.cell>{{ $service->name }}</flux:table.cell>
                            <flux:table.cell align="end">
                                <!-- Placeholder for actions -->
                            </flux:table.cell>
                        </flux:table.row>
                    @endforeach
                </flux:table.rows>
            </flux:table>
        </div>
    </section>
</div>
