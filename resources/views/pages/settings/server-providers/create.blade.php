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
        <form wire:submit="add" class="space-y-8 mx-auto max-w-lg">
            <flux:heading size="xl" level="1">{{ __('Add a Server Provider') }}</flux:heading>

            <flux:separator />

            <flux:input wire:model="name" :label="__('Name')" />

            <flux:select wire:model="type" :label="__('Type')">
                <flux:select.option value=""></flux:select.option>
                <flux:select.option value="DigitalOcean">DigitalOcean</flux:select.option>
            </flux:select>

            <flux:separator variant="subtle" />

            <div class="flex justify-end gap-4">
                <flux:button variant="ghost" href="/settings/server-providers">{{ __('Cancel') }}</flux:button>
                <flux:button variant="primary" type="submit">{{ __('Add Provider') }}</flux:button>
            </div>
        </form>
    @endvolt
</x-layouts.app>
