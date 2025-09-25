<?php

use App\Livewire\Forms\ServerForm;
use App\Models\Server;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Volt\Component;
use Livewire\WithPagination;

new class extends Component {
    use WithPagination;

    public ServerForm $form;

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
    <form wire:submit="save" class="space-y-6">
        <flux:input
            label="{{ __('Server Name') }}"
            wire:model="form.name"
            required
        />
        <flux:select
            label="{{ __('Provider') }}"
            wire:model="form.provider_id"
            required
        >
            <flux:select.option value="">{{ __('Select Provider') }}</flux:select.option>
            @foreach ($this->organization->serverProviders as $provider)
                <flux:select.option :value="$provider->id">{{ $provider->name }}</flux:select.option>
            @endforeach
        </flux:select>
        <flux:select
            label="{{ __('Region') }}"
            wire:model="form.region"
            required
        >
            <flux:select.option value="">{{ __('Select Region') }}</flux:select.option>
            <flux:select.option value="eu-central">eu-central</flux:select.option>
        </flux:select>
        <flux:select
            label="{{ __('Type') }}"
            wire:model="form.type"
            required
        >
            <flux:select.option value="">{{ __('Select Type') }}</flux:select.option>
            <flux:select.option value="cx21">cx21</flux:select.option>
        </flux:select>
        <flux:button type="submit" variant="primary">
            {{ __('Save Server') }}
        </flux:button>
    </form>

    <div class="mt-10">
        <flux:table :paginate="$this->servers">
            <flux:table.columns>
                <flux:table.column>{{ __('Server Name') }}</flux:table.column>
                <flux:table.column>{{ __('Provider') }}</flux:table.column>
                <flux:table.column>{{ __('Region') }}</flux:table.column>
                <flux:table.column>{{ __('Type') }}</flux:table.column>
                <flux:table.column>{{ __('Created At') }}</flux:table.column>
            </flux:table.columns>
            <flux:table.rows>
                @foreach ($this->servers as $server)
                    <flux:table.row :key="$server->id">
                        <flux:table.cell>{{ $server->name }}</flux:table.cell>
                        <flux:table.cell>{{ $server->provider->name ?? '-' }}</flux:table.cell>
                        <flux:table.cell>{{ $server->region }}</flux:table.cell>
                        <flux:table.cell>{{ $server->type }}</flux:table.cell>
                        <flux:table.cell>{{ $server->created_at->format('Y-m-d H:i') }}</flux:table.cell>
                    </flux:table.row>
                @endforeach
            </flux:table.rows>
        </flux:table>
    </div>
</div>
