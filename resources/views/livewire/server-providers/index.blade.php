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
    <form wire:submit="save">
        <div>
            <label for="name">{{ __('Provider Name') }}</label>
            <input id="name" type="text" wire:model="form.name" required>
            @error('form.name') <span>{{ $message }}</span> @enderror
        </div>
        <div>
            <label for="type">{{ __('Provider Type') }}</label>
            <select id="type" wire:model="form.type" required>
                <option value="">{{ __('Select Type') }}</option>
                <option value="aws">AWS</option>
                <option value="azure">Azure</option>
                <option value="gcp">GCP</option>
                <option value="custom">Custom</option>
            </select>
            @error('form.type') <span>{{ $message }}</span> @enderror
        </div>
        <div>
            <label for="meta-region">{{ __('Region') }}</label>
            <input id="meta-region" type="text" wire:model="form.meta.region">
        </div>
        <div>
            <label for="meta-api-key">{{ __('API Key') }}</label>
            <input id="meta-api-key" type="text" wire:model="form.meta.api_key">
        </div>
        <button type="submit">{{ __('Save Provider') }}</button>
    </form>
</div>
