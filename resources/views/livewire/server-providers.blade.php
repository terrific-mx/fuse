<?php

use App\Models\ServerProvider;
use Flux\Flux;
use Livewire\Volt\Component;
use Illuminate\Support\Facades\Auth;
use Facades\App\Services\HetznerService;
use Illuminate\Validation\Rule;

use Livewire\Attributes\Computed;

new class extends Component {
    public string $provider = 'hetzner';
    public string $name = '';
    public array $credentials = [];
    // Consider renaming $credentials to $apiKey or $details for clarity, but keeping for now to avoid breaking changes.

    #[Computed]
    public function providers()
    {
        return $this->organization->serverProviders()->paginate(10);
    }

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
                Rule::in(['hetzner']),
                Rule::unique('server_providers')->where(fn ($query) => $query->where('organization_id', $this->organization->id)),
            ],
            'name' => ['required', 'string'],
            'credentials' => ['required', 'array'], // Consider renaming to 'details' or 'api_key' for clarity
        ];
    }

    public function addProvider()
    {
        $validated = $this->validate();

        // Use HetznerService for API key validation
        if ($validated['provider'] === 'hetzner') {
            if (! HetznerService::validateApiKey($validated['credentials']['api_key'])) {
                return $this->addError('credentials.api_key', __('The Hetzner API key is invalid.'));
            }
        }
        // In future, support other providers and their credential validation here.

        $this->organization->serverProviders()->create($validated);

        Flux::toast(
            heading: __('Provider added'),
            text: __('The server provider was added successfully.'),
            variant: 'success'
        );

        Flux::modal('add-credential')->close();

        $this->provider = 'hetzner';
        $this->name = '';
        $this->credentials = [];
    }

    public function deleteProvider(ServerProvider $provider)
    {
        $this->authorize('delete', $provider);

        $provider->delete();

        Flux::toast(
            heading: __('Provider deleted'),
            text: __('The server provider was deleted.'),
            variant: 'success'
        );
    }
}; ?>

<div>
    <div class="mb-8">
        <flux:heading size="lg">
            {{ __('Server Providers') }}
        </flux:heading>
        <flux:text class="mt-2">
            {{ __('Manage your organization\'s server credentials, including provider details and API keys.') }}
        </flux:text>
    </div>

    <flux:modal.trigger name="add-credential">
        <flux:button icon="plus" variant="primary" class="mb-4">
            {{ __('Add Server Provider') }}
        </flux:button>
    </flux:modal.trigger>

    <flux:modal name="add-credential" variant="flyout" class="min-w-[22rem]">
        <form wire:submit="addProvider" class="space-y-6">
            <div>
                <flux:heading size="lg">{{ __('Add Server Provider') }}</flux:heading>
                <flux:text class="mt-2">
                    {{ __('Enter server provider details and API key.') }}
                </flux:text>
            </div>
            <flux:input
                label="{{ __('Provider') }}"
                placeholder="{{ __('Provider name') }}"
                wire:model="provider"
                required
                disabled
            />
            <flux:input
                label="{{ __('Name') }}"
                placeholder="{{ __('Provider name') }}"
                wire:model="name"
                required
            />
            <flux:input
                label="{{ __('API Key') }}"
                placeholder="{{ __('API key') }}"
                wire:model="credentials.api_key"
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

    <flux:table :paginate="$this->providers()">
        <flux:table.columns>
            <flux:table.column>{{ __('Provider') }}</flux:table.column>
            <flux:table.column>{{ __('Name') }}</flux:table.column>
            <flux:table.column>{{ __('API Key') }}</flux:table.column>
        </flux:table.columns>
        <flux:table.rows>
@foreach ($this->providers() as $serverProvider)
    <flux:table.row :key="$serverProvider->id">
        <flux:table.cell>{{ $serverProvider->provider }}</flux:table.cell>
        <flux:table.cell>{{ $serverProvider->name }}</flux:table.cell>
        <flux:table.cell>{{ $serverProvider->masked_api_key }}</flux:table.cell>
        <flux:table.cell align="end">
            <flux:dropdown position="bottom" align="end">
                <flux:button variant="ghost" size="sm" icon="ellipsis-horizontal" inset="top bottom"></flux:button>
                <flux:menu>
                    <flux:menu.item
                        variant="danger"
                        icon="trash"
                        wire:click="deleteProvider({{ $serverProvider->id }})"
                    >{{ __('Delete') }}</flux:menu.item>
                </flux:menu>
            </flux:dropdown>
        </flux:table.cell>
    </flux:table.row>
@endforeach
        </flux:table.rows>
    </flux:table>
</div>
