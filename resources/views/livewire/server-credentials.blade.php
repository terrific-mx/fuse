<?php

use App\Models\ServerCredential;
use Flux\Flux;
use Livewire\Volt\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\Rule;

use Livewire\Attributes\Computed;

new class extends Component {
    public string $provider = 'hetzner';
    public string $name = '';
    public array $credentials = [];

    #[Computed]
    public function credentials()
    {
        return $this->organization->serverCredentials()->paginate(10);
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
                Rule::unique('server_credentials')->where(fn ($query) => $query->where('organization_id', $this->organization->id)),
            ],
            'name' => ['required', 'string'],
            'credentials' => ['required', 'array'],
        ];
    }

    public function addCredential()
    {
        $validated = $this->validate();

        // Minimal Hetzner API key validation
        if ($validated['provider'] === 'hetzner') {
            $response = Http::withToken($validated['credentials']['api_key'])
                ->timeout(5)
                ->get('https://api.hetzner.cloud/v1/locations');

            if (!$response->successful() || empty($response->json('locations'))) {
                return $this->addError('credentials.api_key', __('The Hetzner API key is invalid.'));
            }
        }

        $this->organization->serverCredentials()->create($validated);

        Flux::toast(
            heading: __('Credential added'),
            text: __('The server credential was added successfully.'),
            variant: 'success'
        );

        Flux::modal('add-credential')->close();

        $this->provider = 'hetzner';
        $this->name = '';
        $this->credentials = [];
    }

    public function deleteCredential(ServerCredential $credential)
    {
        $this->authorize('delete', $credential);

        $credential->delete();

        Flux::toast(
            heading: __('Credential deleted'),
            text: __('The server credential was deleted.'),
            variant: 'success'
        );
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
                wire:model="provider"
                required
                disabled
            />
            <flux:input
                label="{{ __('Name') }}"
                placeholder="{{ __('Credential name') }}"
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

    <flux:table :paginate="$this->credentials()">
        <flux:table.columns>
            <flux:table.column>{{ __('Provider') }}</flux:table.column>
            <flux:table.column>{{ __('Name') }}</flux:table.column>
            <flux:table.column>{{ __('API Key') }}</flux:table.column>
        </flux:table.columns>
        <flux:table.rows>
            @foreach ($this->credentials() as $credential)
                <flux:table.row :key="$credential->id">
                    <flux:table.cell>{{ $credential->provider }}</flux:table.cell>
                    <flux:table.cell>{{ $credential->name }}</flux:table.cell>
                    <flux:table.cell>{{ $credential->credentials['api_key'] ?? __('(none)') }}</flux:table.cell>
                    <flux:table.cell align="end">
                        <flux:dropdown position="bottom" align="end">
                            <flux:button variant="ghost" size="sm" icon="ellipsis-horizontal" inset="top bottom"></flux:button>
                            <flux:menu>
                                <flux:menu.item
                                    variant="danger"
                                    icon="trash"
                                    wire:click="deleteCredential({{ $credential->id }})"
                                >{{ __('Delete') }}</flux:menu.item>
                            </flux:menu>
                        </flux:dropdown>
                    </flux:table.cell>
                </flux:table.row>
            @endforeach
        </flux:table.rows>
    </flux:table>
</div>
