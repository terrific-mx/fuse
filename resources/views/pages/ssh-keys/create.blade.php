<?php

use App\Jobs\AddSshKeyToServers;
use App\Scripts\AddAuthorizedKey;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Laravel\WorkOS\Http\Middleware\ValidateSessionWithWorkOS;
use Livewire\Attributes\Validate;
use Livewire\Volt\Component;

use function Laravel\Folio\middleware;

middleware(['auth', ValidateSessionWithWorkOS::class]);

new class extends Component {
    #[Validate]
    public $name = '';

    #[Validate]
    public $public_key = '';

    protected function rules()
    {
        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('ssh_keys')->where('user_id', Auth::id())
            ],
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

        $validated['public_key'] = trim($validated['public_key']);
        $validated['fingerprint'] = $this->generateFingerprint($validated['public_key']);

        $user = Auth::user();

        $sshKey = $user->sshKeys()->create($validated);

        $user->servers()
            ->where('status', 'provisioned')
            ->each(function ($server) use ($sshKey) {
                AddSshKeyToServers::dispatch($sshKey, $server);
            });

        return $this->redirect('/ssh-keys', navigate: true);
    }

    protected function generateFingerprint(string $publicKey): string
    {
        $keyParts = explode(' ', $publicKey, 3);
        $key = base64_decode($keyParts[1] ?? '');

        if ($key === false) {
            return 'invalid-key';
        }

        $hash = md5($key);
        return implode(':', str_split($hash, 2));
    }
}; ?>

<x-layouts.app>
    @volt('pages.ssh-keys.create')
        <form wire:submit="save" class="space-y-8 mx-auto max-w-lg">
            <flux:heading size="xl" level="1">{{ __('Add a SSH Key') }}</flux:heading>

            <flux:separator />

            <flux:input wire:model="name" :label="__('Name')" />

            <flux:textarea
                wire:model="public_key"
                :label="__('Public Key')"
                :placeholder="__('Paste your public key here (e.g., ssh-rsa AAAAB3NzaC1yc2E...)')"
                class="font-mono"
            />

            <div class="flex justify-end gap-4">
                <flux:button variant="ghost" href="/ssh-keys" wire:navigate>{{ __('Cancel') }}</flux:button>
                <flux:button variant="primary" type="submit">{{ __('Add Key') }}</flux:button>
            </div>
        </form>
    @endvolt
</x-layouts.app>
