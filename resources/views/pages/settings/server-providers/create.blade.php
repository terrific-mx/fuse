<?php

use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Validate;
use Livewire\Volt\Component;

use function Laravel\Folio\middleware;

middleware('auth');

new class extends Component {
    #[Validate]
    public string $name = '';

    #[Validate('required|in:DigitalOcean,FakeServerProvider')]
    public string $type = '';

    #[Validate('required|string')]
    public string $token = '';

    protected function rules()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        return [
            'name' => 'required|max:255|unique:server_providers,name,NULL,id,user_id,'.$user->id,
        ];
    }

    public function add()
    {
        $this->validate();

        /** @var \App\Models\User $user */
        $user = Auth::user();

        $provider = $user->serverProviders()->create([
            'name' => $this->name,
            'type' => $this->type,
            'token' => $this->token,
        ]);

        if (! $provider->client()->valid()) {
            $provider->delete();

            $this->addError('token', 'The given credentials are invalid.');

            return;
        }

        return $this->redirect('/settings/server-providers');
    }
}; ?>

<x-layouts.app>
    @volt('pages.settings.server-providers.create')
        <section class="space-y-6">
            <flux:heading>{{ __('Add Server Provider') }}</flux:heading>

            <form wire:submit="add" class="space-y-6 max-w-sm">
                <flux:input wire:model="name" :label="__('Name')" />
                <flux:select wire:model="type" :label="__('Type')">
                    <flux:select.option value=""></flux:select.option>
                    <flux:select.option value="DigitalOcean">DigitalOcean</flux:select.option>
                </flux:select>
                <flux:input wire:model="token" :label="__('Token')" />
                <flux:button type="submit">{{ __('Add') }}</flux:button>
            </form>
        </section>
    @endvolt
</x-layouts.app>
