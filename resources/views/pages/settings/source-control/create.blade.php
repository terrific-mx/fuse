<?php

use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Validate;
use Livewire\Volt\Component;

new class extends Component {
    #[Validate]
    public string $name = '';

    #[Validate('required|in:GitHub,FakeSourceProvider')]
    public string $type = '';

    #[Validate('required|string')]
    public string $token = '';

    protected function rules()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        return [
            'name' => 'required|max:255|unique:source_providers,name,NULL,id,user_id,'.$user->id,
        ];
    }

    public function add()
    {
        $this->validate();

        /** @var \App\Models\User $user */
        $user = Auth::user();

        $sourceProvider = $user->sourceProviders()->create([
            'name' => $this->name,
            'type' => $this->type,
            'token' => $this->token,
        ]);

        if (! $sourceProvider->client()->valid()) {
            $sourceProvider->delete();

            $this->addError('token', 'The given credentials are invalid.');

            return;
        }

        return $this->redirect('/settings/source-control');
    }
}; ?>

<x-layouts.app>
    @volt('pages.settings.source-control.create')
        <section class="space-y-6">
            <flux:heading>{{ __('Add Source Control Provider') }}</flux:heading>

            <form wire:submit="add" class="space-y-6 max-w-sm">
                <flux:input wire:model="name" :label="__('Name')" />
                <flux:select wire:model="type" :label="__('Type')">
                    <flux:select.option value=""></flux:select.option>
                    <flux:select.option value="GitHub">GitHub</flux:select.option>
                </flux:select>
                <flux:input wire:model="token" :label="__('Token')" />
                <flux:button type="submit">{{ __('Add') }}</flux:button>
            </form>
        </section>
    @endvolt
</x-layouts.app>
