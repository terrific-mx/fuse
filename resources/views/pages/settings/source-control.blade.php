<?php

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Laravel\WorkOS\Http\Middleware\ValidateSessionWithWorkOS;
use Livewire\Volt\Component;

use function Laravel\Folio\middleware;

middleware(['auth', ValidateSessionWithWorkOS::class]);

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
            <div class="flex items-end justify-between gap-4">
                <flux:heading size="xl" level="1">{{ __('Source Control') }}</flux:heading>

                <flux:button href="/settings/source-control/create" variant="primary" class="-my-1">
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
