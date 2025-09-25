<?php

use App\Livewire\Forms\ServerProviderForm;
use App\Models\ServerProvider;
use Illuminate\Support\Facades\Auth;
use Livewire\Volt\Component;
use Livewire\Attributes\Computed;

new class extends Component {
    public ServerProviderForm $form;

    #[Computed]
    public function organization()
    {
        return Auth::user()->currentOrganization;
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
            <flux:select.option value="Hetzner Cloud">Hetzner Cloud</flux:select.option>
            <flux:select.option value="Invalid">{{ __('Invalid') }}</flux:select.option>
        </flux:select>
        <flux:input
            label="{{ __('API Key') }}"
            wire:model="form.meta.token"
        />
        <flux:button type="submit" variant="primary">
            {{ __('Save Provider') }}
        </flux:button>
    </form>
</div>
