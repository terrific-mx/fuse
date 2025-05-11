<?php

use Illuminate\Support\Facades\Auth;
use Laravel\WorkOS\Http\Middleware\ValidateSessionWithWorkOS;
use Livewire\Attributes\Validate;
use Livewire\Volt\Component;

use function Laravel\Folio\middleware;

middleware(['auth', ValidateSessionWithWorkOS::class]);

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
        <form wire:submit="add" class="space-y-8 mx-auto max-w-lg">
            <flux:heading size="xl" level="1">{{ __('Add a Source Control Provider') }}</flux:heading>

            <flux:separator />

            <flux:input wire:model="name" :label="__('Name')" />

            <flux:select wire:model="type" :label="__('Type')">
                <flux:select.option value=""></flux:select.option>
                <flux:select.option value="GitHub">GitHub</flux:select.option>
            </flux:select>

            <flux:input wire:model="token" :label="__('Token')" />

            <flux:separator variant="subtle" />

            <div class="flex justify-end gap-4">
                <flux:button variant="ghost" href="/settings/source-control">{{ __('Cancel') }}</flux:button>
                <flux:button variant="primary" type="submit">{{ __('Add Provider') }}</flux:button>
            </div>
        </form>
    @endvolt
</x-layouts.app>
