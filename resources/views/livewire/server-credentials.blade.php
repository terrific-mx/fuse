<?php

use Livewire\Volt\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Computed;

new class extends Component {
    public string $provider = '';
    public string $name = '';
    public array $credentials = [];

    #[Computed]
    public function organization()
    {
        return Auth::user()->currentOrganization;
    }

    protected function rules(): array
    {
        return [
            'provider' => [
                'required',
                'string',
                Rule::unique('server_credentials')->where(fn ($query) => $query->where('organization_id', $this->organization->id)),
            ],
            'name' => ['required', 'string'],
            'credentials' => ['required', 'array'],
        ];
    }

    public function addCredential()
    {
        $validated = $this->validate();

        $this->organization->serverCredentials()->create($validated);
    }
}; ?>

<div>
    <div class="mb-8">
        <flux:heading size="lg">
            {{ __('Server Credentials') }}
        </flux:heading>
        <flux:text class="mt-2">
            {{ __('Manage your organization\'s server credentials, including provider details and API keys.') }}
        </flux:text>
    </div>

    <flux:modal.trigger name="add-credential">
        <flux:button icon="plus" variant="primary" class="mb-4">
            {{ __('Add Server Credential') }}
        </flux:button>
    </flux:modal.trigger>

    <flux:modal name="add-credential" variant="flyout" class="min-w-[22rem]">
        <form wire:submit="addCredential" class="space-y-6">
            <div>
                <flux:heading size="lg">{{ __('Add Server Credential') }}</flux:heading>
                <flux:text class="mt-2">
                    {{ __('Enter provider details and API key.') }}
                </flux:text>
            </div>
            <flux:input
                label="{{ __('Provider') }}"
                placeholder="{{ __('Provider name') }}"
                wire:model.defer="provider"
                required
            />
            <flux:input
                label="{{ __('Name') }}"
                placeholder="{{ __('Credential name') }}"
                wire:model.defer="name"
                required
            />
            <flux:input
                label="{{ __('API Key') }}"
                placeholder="{{ __('API key') }}"
                wire:model.defer="credentials.api_key"
                required
            />
            <div class="flex gap-2">
                <flux:spacer />
                <flux:modal.close>
                    <flux:button variant="ghost">{{ __('Cancel') }}</flux:button>
                </flux:modal.close>
                <flux:button type="submit" variant="primary">{{ __('Add') }}</flux:button>
            </div>
        </form>
    </flux:modal>
</div>
