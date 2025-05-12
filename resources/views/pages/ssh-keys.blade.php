<?php

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Laravel\WorkOS\Http\Middleware\ValidateSessionWithWorkOS;
use Livewire\Attributes\Validate;
use Livewire\Volt\Component;

use function Laravel\Folio\middleware;

middleware(['auth', ValidateSessionWithWorkOS::class]);

new class extends Component {
    public ?Collection $sshKeys;

    public function mount()
    {
        $this->sshKeys = Auth::user()->sshKeys;
    }
}; ?>

<x-layouts.app>
    @volt('pages.ssh-keys')
        <section class="space-y-6">
            <div class="flex items-end justify-between gap-4">
                <flux:heading size="xl" level="1">{{ __('SSH Keys') }}</flux:heading>

                <flux:button href="/ssh-keys/create" variant="primary" class="-my-1">
                    {{ __('Add Key') }}
                </flux:button>
            </div>

            <flux:separator />

            <flux:table>
                <flux:table.columns>
                    <flux:table.column>{{ __('Name') }}</flux:table.column>
                    <flux:table.column>{{ __('Added') }}</flux:table.column>
                </flux:table.columns>

                <flux:table.rows>
                    @foreach ($sshKeys as $key)
                        <flux:table.row>
                            <flux:table.cell variant="strong">
                                {{ $key->name }}
                            </flux:table.cell>
                            <flux:table.cell>
                                {{ $key->created_at->diffForHumans() }}
                            </flux:table.cell>
                        </flux:table.row>
                    @endforeach
                </flux:table.rows>
            </flux:table>
        </section>
    @endvolt
</x-layouts.app>
