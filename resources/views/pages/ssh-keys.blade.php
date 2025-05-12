<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Laravel\WorkOS\Http\Middleware\ValidateSessionWithWorkOS;
use Livewire\Attributes\Validate;
use Livewire\Volt\Component;

use function Laravel\Folio\middleware;

middleware(['auth', ValidateSessionWithWorkOS::class]);

new class extends Component {
    //
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
        </section>
    @endvolt
</x-layouts.app>
