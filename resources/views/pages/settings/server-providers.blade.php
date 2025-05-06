<?php

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Livewire\Volt\Component;

new class extends Component {
    public ?Collection $serverProviders;

    public function mount()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $this->serverProviders = $user->serverProviders;
    }
}; ?>

<x-layouts.app>
    @volt('pages.settings.server-providers')
        <section class="space-y-6">
            <div class="flex items-end justify-between gap-4">
                <flux:heading size="xl" level="1">{{ __('Server Providers') }}</flux:heading>

                <flux:button href="/settings/server-providers/create" class="-my-1">
                    {{ __('Add provider') }}
                </flux:button>
            </div>

            <flux:separator />

            <flux:table>
                <flux:table.columns>
                    <flux:table.column>{{ __('Name') }}</flux:table.column>
                    <flux:table.column>{{ __('Type') }}</flux:table.column>
                </flux:table.columns>

                <flux:table.rows>
                    @foreach ($serverProviders as $provider)
                        <flux:table.row>
                            <flux:table.cell variant="strong">
                                {{ $provider->name }}
                            </flux:table.cell>
                            <flux:table.cell>
                                {{ $provider->type }}
                            </flux:table.cell>
                        </flux:table.row>
                    @endforeach
                </flux:table.rows>
            </flux:table>
        </section>
    @endvolt
</x-layouts.app>
