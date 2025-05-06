<?php

use App\Jobs\InstallSite;
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

        $site = $this->server->sites()->create([
            'source_provider_id' => $this->source_provider_id,
            'domain' => $this->domain,
            'repository' => $this->repository,
            'branch' => $this->branch,
            'tls' => 'auto',
            'status' => 'creating',
            'type' => 'laravel',
        ]);

        InstallSite::dispatch($site);

        return $this->redirect("/servers/{$this->server->id}");
    }
}; ?>

<x-layouts.app>
    @volt('pages.servers.sites.create')
        <section class="space-y-6">
            <div>
                <flux:link href="/servers/{{ $server->id }}" class="text-sm">
                    {{ __('Back') }}
                </flux:link>
            </div>
            <flux:heading>
                {{ __('Install site') }}
            </flux:heading>
            <form wire:submit="create" class="space-y-5">
                <flux:input wire:model="domain" label="{{ __('Domain') }}" required />
                <flux:select wire:model="source_provider_id" name="source_provider_id" label="{{ __('Source Provider') }}" required>
                    <flux:select.option value=""></flux:select.option>
                    @foreach ($sourceProviders as $provider)
                        <flux:select.option value="{{ $provider->id }}">{{ $provider->name }}</flux:select.option>
                    @endforeach
                </flux:select>
                <flux:input wire:model="repository" name="repository" label="{{ __('Repository') }}" required />
                <flux:input wire:model="branch" name="branch" label="{{ __('Branch') }}" required />
                <flux:button type="submit">{{ __('Add') }}</flux:button>
            </form>
        </section>
    @endvolt
</x-layouts.app>
