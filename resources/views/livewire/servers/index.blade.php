<?php

use App\Livewire\Forms\ServerForm;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Volt\Component;
use Livewire\WithPagination;

new class extends Component {
    use WithPagination;

    public ServerForm $form;

    public function mount()
    {
        $this->form->setOrganization($this->organization);
    }

    #[Computed]
    public function organization()
    {
        return Auth::user()->currentOrganization;
    }

    #[Computed]
    public function servers()
    {
        return $this->organization->servers()
            ->orderByDesc('created_at')
            ->paginate(10);
    }

    public function save()
    {
        $this->form->store();
    }
}; ?>

<x-slot:breadcrumbs>
    <flux:breadcrumbs>
        <flux:breadcrumbs.item>{{ __('Servers') }}</flux:breadcrumbs.item>
    </flux:breadcrumbs>
</x-slot:breadcrumbs>

<div>
    <header class="flex flex-wrap items-end justify-between gap-4">
        <div class="max-sm:w-full sm:flex-1">
            <flux:heading size="lg">
                {{ __('Servers') }}
            </flux:heading>
            <flux:text class="mt-2 max-w-prose">
                {{ __('Manage the servers for your organization. Servers are used to host your sites and deployments.') }}
            </flux:text>
        </div>
        <flux:modal.trigger name="add-server">
            <flux:button variant="primary">
                {{ __('Add server') }}
            </flux:button>
        </flux:modal.trigger>
    </header>

    <div class="mt-10">
        <flux:table :paginate="$this->servers">
            <flux:table.columns>
                <flux:table.column>{{ __('Server name') }}</flux:table.column>
                <flux:table.column>{{ __('IP Address') }}</flux:table.column>
                <flux:table.column>{{ __('Created at') }}</flux:table.column>
                <flux:table.column align="end">{{ __('Status') }}</flux:table.column>
             </flux:table.columns>
            <flux:table.rows>
                @foreach ($this->servers as $server)
                    <flux:table.row :key="$server->id">
                        <flux:table.cell><flux:link :href="route('servers.show', $server)" wire:navigate>{{ $server->name }}</flux:link></flux:table.cell>
                        <flux:table.cell>{{ $server->ip_address }}</flux:table.cell>
                        <flux:table.cell>{{ $server->created_at->format('Y-m-d H:i') }}</flux:table.cell>
                        <flux:table.cell align="end">{{ __($server->status) }}</flux:table.cell>
                     </flux:table.row>
                @endforeach
            </flux:table.rows>
        </flux:table>
    </div>

    <flux:modal name="add-server" variant="flyout" class="max-w-md">
        <form wire:submit="save" class="space-y-6">
            <div>
                <flux:heading size="lg">{{ __('Add server') }}</flux:heading>
                <flux:text class="mt-2 max-w-prose">
                    {{ __('Provide a name, IP address, and select SSH keys for this server.') }}
                </flux:text>
            </div>

            <flux:callout icon="exclamation-triangle" variant="warning">
                <flux:callout.heading>{{ __('Important: Ubuntu 24.04 required') }}</flux:callout.heading>
                <flux:callout.text>{{ __('Please ensure your server is provisioned with Ubuntu version 24.04 for compatibility.') }}</flux:callout.text>
            </flux:callout>

            <flux:callout icon="information-circle" variant="secondary">
                <flux:callout.heading>{{ __('Organization SSH key required') }}</flux:callout.heading>
                <flux:callout.text>{{ __('To provision your server, ensure the organization SSH public key is installed on the server you create with your cloud provider.') }}</flux:callout.text>
                <x-slot name="actions">
                    <flux:input
                        icon="key"
                        value="{{ $this->organization->ssh_public_key }}"
                        readonly
                        copyable
                    />
                </x-slot>
            </flux:callout>

            <flux:input
                label="{{ __('Name') }}"
                description="{{ __('A label to identify this server.') }}"
                wire:model="form.name"
                required
            />

            <flux:input
                label="{{ __('IP address') }}"
                description="{{ __('The public IP address of your server.') }}"
                wire:model="form.ip_address"
                required
            />

            <flux:pillbox
                wire:model="form.ssh_keys"
                multiple
                searchable
                label="{{ __('SSH keys') }}"
                description="{{ __('Select one or more SSH keys to install on this server.') }}"
            >
                @foreach ($this->organization->sshKeys as $key)
                    <flux:pillbox.option value="{{ $key->id }}">{{ $key->name }}</flux:pillbox.option>
                @endforeach
            </flux:pillbox>
            <div class="flex">
                <flux:spacer />
                <flux:button type="submit" variant="primary">
                    {{ __('Add server') }}
                </flux:button>
            </div>
        </form>
    </flux:modal>
</div>
