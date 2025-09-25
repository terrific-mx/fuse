<?php

use App\Livewire\Forms\SourceProviderForm;
use App\Models\SourceProvider;
use Illuminate\Support\Facades\Auth;
use Livewire\Volt\Component;
use Livewire\Attributes\Computed;
use Livewire\WithPagination;

new class extends Component {
    use WithPagination;

    public SourceProviderForm $form;

    #[Computed]
    public function organization()
    {
        return Auth::user()->currentOrganization;
    }

    #[Computed]
    public function providers()
    {
        return $this->organization->sourceProviders()
            ->orderByDesc('created_at')
            ->paginate(10);
    }

    public function save()
    {
        $this->form->store($this->organization);
    }
}; ?>

<div>
    <form wire:submit="save" class="space-y-6">
        <flux:input
            label="{{ __('Provider Name') }}"
            wire:model="form.name"
            required
        />
        <flux:select
            label="{{ __('Provider Type') }}"
            wire:model="form.type"
            required
        >
            <flux:select.option value="">{{ __('Select Type') }}</flux:select.option>
            <flux:select.option value="GitHub">GitHub</flux:select.option>
        </flux:select>
        <flux:input
            label="{{ __('API Key') }}"
            wire:model="form.meta.token"
        />
        <flux:button type="submit" variant="primary">
            {{ __('Save Provider') }}
        </flux:button>
    </form>

    <div class="mt-10">
        <flux:table :paginate="$this->providers">
            <flux:table.columns>
                <flux:table.column>{{ __('Provider Name') }}</flux:table.column>
                <flux:table.column>{{ __('Type') }}</flux:table.column>
                <flux:table.column>{{ __('Created At') }}</flux:table.column>
            </flux:table.columns>
            <flux:table.rows>
                @foreach ($this->providers as $provider)
                    <flux:table.row :key="$provider->id">
                        <flux:table.cell>{{ $provider->name }}</flux:table.cell>
                        <flux:table.cell>{{ $provider->type }}</flux:table.cell>
                        <flux:table.cell>{{ $provider->created_at->format('Y-m-d H:i') }}</flux:table.cell>
                    </flux:table.row>
                @endforeach
            </flux:table.rows>
        </flux:table>
    </div>
</div>
