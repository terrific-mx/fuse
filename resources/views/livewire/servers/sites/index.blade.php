<?php

use App\Livewire\Forms\SiteForm;
use App\Models\Server;
use Livewire\Attributes\Computed;
use Livewire\Volt\Component;

new class extends Component {
    public Server $server;

    public SiteForm $form;

    public function mount()
    {
        $this->authorize('view', $this->server);
    }

    public function save()
    {
        $this->form->store($this->server);
    }

    #[Computed]
    public function sites()
    {
        return $this->server->sites()->orderByDesc('created_at')->get();
    }
}; ?>

<x-slot:breadcrumbs>
    @include('partials.server-breadcrumbs', ['server' => $server, 'current' => __('Sites')])
</x-slot:breadcrumbs>

<div>
    @include('partials.server-heading')
    <div class="flex items-start max-md:flex-col">
        @include('partials.server-navbar', ['server' => $server])
        <flux:separator class="md:hidden" />
        <div class="flex-1 self-stretch max-md:pt-6">
            <section>
                <header class="flex flex-wrap items-end justify-between gap-4">
                    <div class="max-sm:w-full sm:flex-1">
                        <flux:heading>{{ __('Sites') }}</flux:heading>
                        <flux:text class="mt-2 max-w-prose">{{ __('Manage all the sites hosted on this server. Add new sites, view details, and configure deployment settings.') }}</flux:text>
                    </div>
                    <flux:modal.trigger name="add-site">
                        <flux:button variant="primary">{{ __('Add site') }}</flux:button>
                    </flux:modal.trigger>
                </header>

                <div class="mt-4">
                    <flux:table>
                        <flux:table.columns>
                            <flux:table.column>{{ __('Hostname') }}</flux:table.column>
                            <flux:table.column>{{ __('PHP version') }}</flux:table.column>
                            <flux:table.column>{{ __('Repository URL') }}</flux:table.column>
                            <flux:table.column>{{ __('Repository Branch') }}</flux:table.column>
                        </flux:table.columns>
                        <flux:table.rows>
                            @foreach ($this->sites as $site)
                                <flux:table.row :key="$site->id">
                                    <flux:table.cell><flux:link :href="route('servers.sites.show', ['server' => $server, 'site' => $site])">{{ $site->hostname }}</flux:link></flux:table.cell>
                                    <flux:table.cell>{{ $site->php_version }}</flux:table.cell>
                                    <flux:table.cell>{{ $site->repository_url }}</flux:table.cell>
                                    <flux:table.cell>{{ $site->repository_branch }}</flux:table.cell>
                                </flux:table.row>
                            @endforeach
                        </flux:table.rows>
                    </flux:table>
                </div>
            </section>
            <flux:modal name="add-site" variant="flyout">
                <form wire:submit="save" class="space-y-6">
                    <div>
                        <flux:heading size="lg">{{ __('Add site') }}</flux:heading>
                        <flux:text class="mt-2">{{ __('Create a new site on this server.') }}</flux:text>
                    </div>

                    <flux:input wire:model="form.hostname" :label="__('Hostname')" :description="__('The domain or subdomain for this site (e.g. example.com).')" required />

                    <flux:select wire:model="form.php_version" :label="__('PHP version')" :description="__('The PHP runtime version to use for this site.')" required>
                        <flux:select.option></flux:select.option>
                        <flux:select.option value="8.4">8.4</flux:select.option>
                        <flux:select.option value="8.3">8.3</flux:select.option>
                        <flux:select.option value="8.1">8.1</flux:select.option>
                    </flux:select>
                    <flux:fieldset>
                        <flux:legend class="text-sm">{{ __('Repository') }}</flux:legend>

                        <div class="space-y-6">
                            <flux:input wire:model="form.repository_url" :label="__('Repository URL')" :description="__('The Git repository URL to deploy from (e.g. https://github.com/your/repo.git).')" />
                            <flux:input wire:model="form.repository_branch" :label="__('Repository Branch')" :description="__('The branch to deploy from (e.g. main, develop).')" />
                        </div>
                    </flux:fieldset>

                    <div class="flex">
                        <flux:spacer />
                        <flux:button type="submit" variant="primary">{{ __('Add Site') }}</flux:button>
                    </div>
                </form>
            </flux:modal>
        </div>
    </div>
</div>
