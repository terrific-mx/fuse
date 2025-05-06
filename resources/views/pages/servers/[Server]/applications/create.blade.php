<?php

use App\Jobs\InstallApplication;
use App\Models\Server;
use App\Scripts\InstallCaddyfile;
use App\Scripts\UpdateCaddyImports;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Validate;
use Livewire\Volt\Component;

new class extends Component {
    public Server $server;
    public ?Collection $sourceProviders;

    #[Validate('required|max:255')]
    public string $domain = '';

    #[Validate]
    public ?int $source_provider_id = null;

    #[Validate]
    public string $repository = '';

    #[Validate]
    public string $branch = '';

    protected function rules()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $sourceProvider = $user->sourceProviders()->find($this->source_provider_id);

        return [
            'source_provider_id' => [
                'required',
                "exists:source_providers,id,user_id,{$user->id}"
            ],
            'repository' => [
                'required',
                function ($attribute, $value, $fail) use ($sourceProvider) {
                    if (!$sourceProvider->client()->validRepository($value)) {
                        $fail(__('The selected repository is invalid for the chosen source provider.'));
                    }
                }
            ],
            'branch' => [
                'required',
                function ($attribute, $value, $fail) use ($sourceProvider) {
                    if (!$sourceProvider->client()->validBranch($value, $this->repository)) {
                        $fail(__('The selected repository is invalid for the chosen source provider.'));
                    }
                }
            ],
        ];
    }

    public function mount()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $this->sourceProviders = $user->sourceProviders;
    }

    public function create()
    {
        $this->validate();

        $application = $this->server->applications()->create([
            'source_provider_id' => $this->source_provider_id,
            'domain' => $this->domain,
            'repository' => $this->repository,
            'branch' => $this->branch,
            'tls' => 'auto',
            'status' => 'creating',
            'type' => 'laravel',
        ]);

        InstallApplication::dispatch($application);

        return $this->redirect("/servers/{$this->server->id}");
    }
}; ?>

<x-layouts.app>
    @volt('pages.servers.applications.create')
        <form wire:submit="create" class="space-y-8 mx-auto max-w-lg">
            <flux:heading size="xl" level="1">{{ __('Add a New Application') }}</flux:heading>

            <flux:separator />

            <flux:input wire:model="domain" label="{{ __('Domain') }}" placeholder="example.com" required />

            <flux:select wire:model="source_provider_id" name="source_provider_id" label="{{ __('Source Provider') }}" required>
                <flux:select.option value=""></flux:select.option>
                @foreach ($sourceProviders as $provider)
                    <flux:select.option value="{{ $provider->id }}">{{ $provider->name }}</flux:select.option>
                @endforeach
            </flux:select>

            <flux:input wire:model="repository" name="repository" label="{{ __('Repository') }}" placeholder="terrific-mx/fuse" required />

            <flux:input wire:model="branch" name="branch" label="{{ __('Branch') }}" placeholder="main" required />

            <flux:separator variant="subtle" />

            <div class="flex justify-end gap-4">
                <flux:button variant="ghost" href="/servers/{{ $server->id }}">{{ __('Cancel') }}</flux:button>
                <flux:button variant="primary" type="submit">{{ __('Add Application') }}</flux:button>
            </div>
        </form>
    @endvolt
</x-layouts.app>
