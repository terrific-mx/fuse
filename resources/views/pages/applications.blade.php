<?php

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Laravel\WorkOS\Http\Middleware\ValidateSessionWithWorkOS;
use Livewire\Volt\Component;

use function Laravel\Folio\middleware;

middleware('auth', ValidateSessionWithWorkOS::class);

new class extends Component {
    public ?Collection $applications;

    public function mount() {
        $user = Auth::user();

        $this->applications = $user->applications;
    }
}; ?>

<x-layouts.app>
    @volt('pages.applications')
        <section class="space-y-6">
            <div class="flex items-end justify-between gap-4">
                <flux:heading size="xl" level="1">{{ __('Applications') }}</flux:heading>
            </div>

            <flux:separator />

            <flux:table>
                <flux:table.columns>
                    <flux:table.column>{{ __('Domain') }}</flux:table.column>
                    <flux:table.column>{{ __('Status') }}</flux:table.column>
                </flux:table.columns>

                <flux:table.rows>
                    @foreach ($applications as $application)
                        <flux:table.row>
                            <flux:table.cell variant="strong">
                                <flux:link href="/applications/{{ $application->id }}" wire:navigate>{{ $application->domain }}</flux:link>
                            </flux:table.cell>
                            <flux:table.cell><flux:badge color="green" size="sm" inset="top bottom">{{ $application->status }}</flux:badge></flux:table.cell>
                        </flux:table.row>
                    @endforeach
                </flux:table.rows>
            </flux:table>
        </section>
    @endvolt
</x-layouts.app>
