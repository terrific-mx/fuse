<?php

use Illuminate\Support\Facades\Auth;
use Laravel\WorkOS\Http\Middleware\ValidateSessionWithWorkOS;
use Livewire\Attributes\Validate;
use Livewire\Volt\Component;

use function Laravel\Folio\middleware;

middleware(['auth', ValidateSessionWithWorkOS::class]);

new class extends Component {
    #[Validate(['required', 'string', 'max:255'])]
    public $name = '';

    #[Validate]
    public $public_key = '';

    protected function rules()
    {
        return [
            'public_key' => [
                'required',
                'string',
                'max:4096',
                function ($attribute, $value, $fail) {
                    if (!str_starts_with($value, 'ssh-rsa ') &&
                        !str_starts_with($value, 'ssh-ed25519 ') &&
                        !str_starts_with($value, 'ecdsa-sha2-nistp')) {
                        $fail('The public key must be a valid SSH key (starting with ssh-rsa, ssh-ed25519, or ecdsa-sha2-nistp).');
                    }
                }
            ],
        ];
    }

    public function save()
    {
        $validated = $this->validate();

        Auth::user()->sshKeys()->create($validated);
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
        </section>
    @endvolt
</x-layouts.app>
