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

<div>
    <flux:callout icon="information-circle" variant="secondary">
        <flux:callout.heading>{{ __('Organization SSH Key Required') }}</flux:callout.heading>
        <flux:callout.text>{{ __('To provision your server, ensure the organization SSH public key is installed on the server you create with your cloud provider.') }}</flux:callout.text>
    </flux:callout>
    <div class="my-6">
        <flux:input
            icon="key"
            label="{{ __('Organization SSH Public Key') }}"
            value="{{ $this->organization->ssh_public_key }}"
            readonly
            copyable
        />
    </div>
    <form wire:submit="save" class="space-y-6">
        <flux:input
            label="{{ __('Server Name') }}"
            wire:model="form.name"
            required
        />
        <flux:input
            label="{{ __('Server IP Address') }}"
            wire:model="form.ip_address"
            required
        />
        <flux:button type="submit" variant="primary">
            {{ __('Save Server') }}
        </flux:button>
    </form>

    <div class="mt-10">
        <flux:table :paginate="$this->servers">
            <flux:table.columns>
                <flux:table.column>{{ __('Server Name') }}</flux:table.column>
                <flux:table.column>{{ __('IP Address') }}</flux:table.column>
                 <flux:table.column>{{ __('Created At') }}</flux:table.column>
                 <flux:table.column>{{ __('Status') }}</flux:table.column>
             </flux:table.columns>
            <flux:table.rows>
                @foreach ($this->servers as $server)
                    <flux:table.row :key="$server->id">
                        <flux:table.cell><flux:link :href="route('servers.show', $server)" wire:navigate>{{ $server->name }}</flux:link></flux:table.cell>
                        <flux:table.cell>{{ $server->ip_address }}</flux:table.cell>
                         <flux:table.cell>{{ $server->created_at->format('Y-m-d H:i') }}</flux:table.cell>
                         <flux:table.cell>{{ __($server->status) }}</flux:table.cell>
                     </flux:table.row>
                @endforeach
            </flux:table.rows>
        </flux:table>
    </div>
</div>
