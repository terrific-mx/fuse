<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Computed;
use Livewire\Volt\Component;

new class extends Component {
    public string $name = '';

    #[Computed]
    public function organization()
    {
        return Auth::user()->currentOrganization;
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
        ];
    }

    public function createServer(): void
    {
        $this->validate();

        $this->organization->servers()->create([
            'name' => $this->name,
        ]);

        $this->name = '';
    }
}; ?>

<form wire:submit="createServer">
    <label for="name">{{ __('Server Name') }}</label>
    <input id="name" type="text" wire:model="name" :placeholder="__('Enter server name')">
    <button type="submit">{{ __('Create Server') }}</button>
    @error('name')
        <div class="error">{{ $message }}</div>
    @enderror
</form>
