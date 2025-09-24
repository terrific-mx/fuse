<?php

use App\Models\Server;
use Facades\App\Services\HetznerService;
use Flux\Flux;
use Livewire\Volt\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Computed;

new class extends Component {
    public string $name = '';
    public string $serverType = '';
    public string $location = '';
    public array $locations = [];

    public function mount(): void
    {
        $this->locations = HetznerService::getLocations();
    }

    #[Computed]
    public function serverTypes(): array
    {
        if (! $this->location) {
            return [];
        }

        return array_values(array_filter(
            HetznerService::getServerTypes(),
            fn ($type) => in_array($this->location, $type['locations'])
        ));
    }

    #[Computed]
    public function organization()
    {
        return Auth::user()->currentOrganization;
    }

    #[Computed]
    public function servers()
    {
        return $this->organization->servers()->paginate(10);
    }

    protected function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'max:253',
                'alpha_dash',
                Rule::unique('servers', 'name')->where(fn ($q) => $q->where('organization_id', $this->organization->id)),
            ],
             'serverType' => [
                'required',
                'string',
                Rule::in(array_column($this->serverTypes, 'name')),
             ],
            'location' => [
                'required',
                'string',
                Rule::in(array_column($this->locations, 'name')),
            ],
        ];
    }

    public function createServer(): void
    {
        $this->validate();

        $apiKey = $this->organization->serverCredentials()
            ->where('provider', 'hetzner')
            ->latest()
            ->first()
            ->credentials['api_key'];

        $hetzner = HetznerService::createServer(
            $apiKey,
            $this->name,
            $this->serverType,
            $this->location
        );

        if (!empty($hetzner['error'])) {
            Flux::toast(
                heading: __('Server creation failed'),
                text: $hetzner['error'],
                variant: 'danger'
            );
            return;
        }

        $server = $this->organization->servers()->create([
            'name' => $this->name,
            'provider_id' => $hetzner['provider_id'],
            'ip_address' => $hetzner['ip_address'],
            'status' => $hetzner['status'],
        ]);

        Flux::toast(
            heading: __('Server created'),
            text: __('The server was created successfully.'),
            variant: 'success'
        );

        Flux::modal('add-server')->close();

        $this->name = '';
    }

    public function deleteServer(Server $server): void
    {
        $this->authorize('delete', $server);
        $server->delete();

        Flux::toast(
            heading: __('Server deleted'),
            text: __('The server was deleted.'),
            variant: 'success'
        );
    }
}; ?>

<div>
    <div class="mb-8">
        <flux:heading size="lg">
            {{ __('Servers') }}
        </flux:heading>
        <flux:text class="mt-2">
            {{ __('Manage your organization\'s servers.') }}
        </flux:text>
    </div>

    <flux:modal.trigger name="add-server">
        <flux:button icon="plus" variant="primary" class="mb-4">
            {{ __('Add Server') }}
        </flux:button>
    </flux:modal.trigger>

    <flux:modal name="add-server" variant="flyout" class="min-w-[22rem]">
        <form wire:submit="createServer" class="space-y-6">
            <div>
                <flux:heading size="lg">{{ __('Add Server') }}</flux:heading>
                <flux:text class="mt-2 max-w-sm">
                    {{ __('Choose a location and server type. Server types show architecture, CPU, cores, and memory. Enter a name for your new server.') }}
                </flux:text>
            </div>
            <flux:input
                label="{{ __('Name') }}"
                placeholder="{{ __('Server name') }}"
                wire:model="name"
                required
            />
            <flux:select label="{{ __('Location') }}" wire:model.live="location" variant="listbox" :placeholder="__('Select a location')">
                @foreach ($this->locations as $loc)
                    <flux:select.option value="{{ $loc['name'] }}">{{ $loc['city'] }} ({{ $loc['name'] }})</flux:select.option>
                @endforeach
            </flux:select>
            <flux:select label="{{ __('Server Type') }}" wire:model="serverType" wire:key="{{ $location }}" variant="listbox" :disabled="empty($this->serverTypes)" :placeholder="empty($this->serverTypes) ? __('Select a location first') : __('Select a server type')">
                @foreach ($this->serverTypes as $type)
                    <flux:select.option value="{{ $type['name'] }}" :selected="$loop->first">
                        {{ strtoupper($type['name']) }} ({{ $type['architecture'] }}, {{ $type['cores'] }} {{ __('cores') }}, {{ $type['cpu_type'] }} CPU, {{ $type['memory'] }}GB RAM)
                    </flux:select.option>
                @endforeach
            </flux:select>
            <div class="flex gap-2">
                <flux:spacer />
                <flux:modal.close>
                    <flux:button variant="ghost">{{ __('Cancel') }}</flux:button>
                </flux:modal.close>
                <flux:button type="submit" variant="primary">{{ __('Create') }}</flux:button>
            </div>
        </form>
    </flux:modal>

    <flux:table :paginate="$this->servers()">
        <flux:table.columns>
            <flux:table.column>{{ __('Name') }}</flux:table.column>
            <flux:table.column>{{ __('Created At') }}</flux:table.column>
        </flux:table.columns>
        <flux:table.rows>
            @foreach ($this->servers() as $server)
                <flux:table.row :key="$server->id">
                    <flux:table.cell>{{ $server->name }}</flux:table.cell>
                    <flux:table.cell>{{ $server->created_at->format('Y-m-d H:i') }}</flux:table.cell>
                    <flux:table.cell align="end">
                        <flux:dropdown position="bottom" align="end">
                            <flux:button variant="ghost" size="sm" icon="ellipsis-horizontal" inset="top bottom"></flux:button>
                            <flux:menu>
                                <flux:menu.item
                                    variant="danger"
                                    icon="trash"
                                    wire:click="deleteServer({{ $server->id }})"
                                >{{ __('Delete') }}</flux:menu.item>
                            </flux:menu>
                        </flux:dropdown>
                    </flux:table.cell>
                </flux:table.row>
            @endforeach
        </flux:table.rows>
    </flux:table>
</div>
