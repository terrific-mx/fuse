<?php

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Livewire\Volt\Component;

use function Laravel\Folio\middleware;

middleware('auth');

new class extends Component {
    public ?Collection $servers;

    public function mount()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $this->servers = $user->servers;
    }
}; ?>

<x-layouts.app>
    @volt('pages.servers')
        <section class="space-y-6">
            <div class="flex items-end justify-between gap-4">
                <flux:heading size="xl" level="1">{{ __('Servers') }}</flux:heading>

                <flux:button href="/servers/provision" class="-my-1">
                    {{ __('Provision') }}
                </flux:button>
            </div>

            <flux:separator />

            <flux:table class="max-w-lg">
                <flux:table.columns>
                    <flux:table.column>{{ __('Name') }}</flux:table.column>
                    <flux:table.column>{{ __('IP Address') }}</flux:table.column>
                    <flux:table.column>{{ __('Status') }}</flux:table.column>
                </flux:table.columns>

                <flux:table.rows>
                    @foreach ($servers as $server)
                        <flux:table.row>
                            <flux:table.cell variant="strong">
                                <flux:link href="/servers/{{ $server->id }}">{{ $server->name }}</flux:link>
                            </flux:table.cell>
                            <flux:table.cell>{{ $server->public_address }}</flux:table.cell>
                            <flux:table.cell><flux:badge color="green" size="sm" inset="top bottom">{{ $server->status }}</flux:badge></flux:table.cell>
                        </flux:table.row>
                    @endforeach
                </flux:table.rows>
            </flux:table>
        </section>
    @endvolt
</x-layouts.app>
