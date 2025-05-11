<?php

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Validate;
use Livewire\Volt\Component;

new class extends Component {
    public ?Collection $serverProviders;
    public array $availableRegions = [];
    public array $availableSizes = [];

    #[Validate]
    public string $name = '';

    #[Validate]
    public string $size = '';

    #[Validate]
    public string $region = '';

    #[Validate]
    public string $server_provider_id = '';

    protected function rules()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        return [
            'name' => 'required|string|alpha_dash|max:255|unique:servers,name,NULL,id,user_id,'.$user->id,
            'server_provider_id' => ['required', Rule::exists('server_providers', 'id')->where(function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })],
            'region' => [
                'required',
                'string',
                function ($attribute, $value, $fail) use ($user) {
                    if (!$this->server_provider_id) {
                        return;
                    }

                    $provider = $user->serverProviders()->find($this->server_provider_id);

                    if ($provider && !$provider->validRegion($this->region)) {
                        $fail('The provided region is invalid.');
                    }
                }
            ],
            'size' => [
                'required',
                'string',
                function ($attribute, $value, $fail) use ($user) {
                    if (!$this->server_provider_id) {
                        return;
                    }

                    if (!$this->region) {
                        return;
                    }

                    $provider = $user->serverProviders()->find($this->server_provider_id);

                    if ($provider && !$provider->validSize($this->size, $this->region)) {
                        $fail('The provided size is invalid.');
                    }
                }
            ],
        ];
    }

    public function mount()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $this->serverProviders = $user->serverProviders;
    }

    public function updatedServerProviderId($value)
    {
        $this->reset('region', 'size');
        $this->resetErrorBag();

        $provider = Auth::user()->serverProviders()->find($value);

        if ($provider) {
            $this->availableRegions = $provider->regions();
        }
    }

    public function updatedRegion($value)
    {
        $this->reset('size');
        $this->resetErrorBag();

        $provider = Auth::user()->serverProviders()->find($this->server_provider_id);

        if ($provider) {
            $this->availableSizes = $provider->sizes($value);
        }
    }

    public function provision()
    {
        $this->validate();

        /** @var \App\Models\User $user */
        $user = Auth::user();

        $user->provisionServer(
            provider: $user->serverProviders()->find($this->server_provider_id),
            name: $this->name,
            size: $this->size,
            region: $this->region,
        );

        return $this->redirect('/servers');
    }
}; ?>

<x-layouts.app>
    @volt('pages.servers.provision')
        <form wire:submit="provision" class="space-y-8 mx-auto max-w-lg">
            <flux:heading size="xl" level="1">{{ __('Provision a New Server') }}</flux:heading>

            <flux:separator />

            <flux:input wire:model="name" :label="__('Name')" />

            <flux:select
                wire:model.live="server_provider_id"
                :label="__('Server Provider')"
                :placeholder="__('Choose an option...')"
            >
                @foreach ($serverProviders as $provider)
                    <flux:select.option value="{{ $provider->id }}">{{ "{$provider->name} ({$provider->type})" }}</flux:select.option>
                @endforeach
            </flux:select>

            <flux:select
                wire:model.live="region"
                wire:key="{{ $server_provider_id }}"
                :label="__('Region')"
                :placeholder="__('Choose an option...')"
            >
                @foreach ($availableRegions as $name => $description)
                    <flux:select.option value="{{ $name }}">{{ $description }}</flux:select.option>
                @endforeach
            </flux:select>

            <flux:select
                wire:model="size"
                wire:key="{{ $region }}"
                :label="__('Size')"
                :placeholder="__('Choose an option...')"
            >
                @foreach ($availableSizes as $name => $description)
                    <flux:select.option value="{{ $name }}">{{ $description }}</flux:select.option>
                @endforeach
            </flux:select>

            <flux:separator variant="subtle" />

            <div class="flex justify-end gap-4">
                <flux:button variant="ghost" href="/servers">{{ __('Cancel') }}</flux:button>
                <flux:button variant="primary" type="submit">{{ __('Provision') }}</flux:button>
            </div>
        </form>
    @endvolt
</x-layouts.app>
