<?php

use App\Models\Server;
use App\Scripts\InstallCaddyfile;
use App\Scripts\UpdateCaddyImports;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Validate;
use Livewire\Volt\Component;

new class extends Component {
    public Server $server;

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

    public function create()
    {
        $this->validate();

        $site = $this->server->sites()->create([
            'source_provider_id' => $this->source_provider_id,
            'domain' => $this->domain,
            'repository' => $this->repository,
            'branch' => $this->branch,
            'status' => 'creating',
        ]);

        $this->server->run(new InstallCaddyfile($site));

        $site->update(['status' => 'installed']);

        $this->server->run(new UpdateCaddyImports($this->server));
    }
}; ?>

<x-layouts.app>
    @volt('pages.servers.sites.create')
        <section>

        </section>
    @endvolt
</x-layouts.app>
