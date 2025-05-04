<?php

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Livewire\Volt\Component;

new class extends Component {
    public ?Collection $sourceProviders;

    public function mount()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $this->sourceProviders = $user->sourceProviders;
    }
}; ?>

<x-layouts.app>
    @volt('pages.settings.source-control')
        <section class="space-y-6">
            <flux:heading>{{ __('Source Control') }}</flux:heading>
            <flux:button href="/settings/source-control/create">{{ __('Add provider') }}</flux:button>
            <flux:table>
                <flux:table.columns>
                    <flux:table.column>{{ __('Name') }}</flux:table.column>
                    <flux:table.column>{{ __('Type') }}</flux:table.column>
                </flux:table.columns>

                <flux:table.rows>
                    @foreach ($sourceProviders as $provider)
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
