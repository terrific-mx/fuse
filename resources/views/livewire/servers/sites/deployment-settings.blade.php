<?php

use App\Livewire\Forms\DeploymentSettingsForm;
use App\Models\Server;
use App\Models\Site;
use Livewire\Volt\Component;

new class extends Component {
    public Server $server;

    public Site $site;

    public DeploymentSettingsForm $form;

    public function mount(): void
    {
        $this->authorize('view', $this->server);

        $this->authorize('view', $this->site);

        $this->form->setSite($this->site);
    }

    public function save(): void
    {
        $this->form->update();
    }
}; ?>

<x-slot:breadcrumbs>
    @include('partials.site-breadcrumbs', ['server' => $server, 'site' => $site, 'current' => __('Deployment settings')])
</x-slot:breadcrumbs>

<div>
    @include('partials.site-heading')
    <div class="flex items-start max-md:flex-col">
        @include('partials.site-navbar', ['server' => $server, 'site' => $site])
        <flux:separator class="md:hidden" />
        <div class="flex-1 self-stretch max-md:pt-6">
            <header>
                <flux:heading>{{ __('Deployment Settings') }}</flux:heading>
                <flux:text class="mt-2 max-w-prose">
                    {{ __('Manage shared files, writable directories, and deployment scripts for this site.') }}
                </flux:text>
            </header>
            <form wire:submit="save" class="max-w-lg space-y-6 mt-6">
                <flux:textarea
                    name="form.shared_directories"
                    :label="__('Directories Shared Across Deployments')"
                    :description="__('List the directories to be shared across deployments. Enter one directory per line.')"
                    class="font-mono"
                    rows="3"
                    wire:model="form.shared_directories"
                />

                <flux:textarea
                    name="form.shared_files"
                    :label="__('Files Shared Across Deployments')"
                    :description="__('List the files to be shared across deployments. Enter one file per line.')"
                    class="font-mono"
                    rows="3"
                    wire:model="form.shared_files"
                />

                <flux:textarea
                    name="form.writable_directories"
                    :label="__('Writable Directories for Webserver')"
                    :description="__('List the directories that should be writable by the webserver. Enter one directory per line.')"
                    class="font-mono"
                    rows="10"
                    wire:model="form.writable_directories"
                />

                <flux:textarea
                    name="form.script_before_deploy"
                    :label="__('Script to Run Before Deploy')"
                    :description="__('This script will be executed just before updating the git repository during deployment.')"
                    class="font-mono min-h-[220px]"
                    rows="10"
                    wire:model="form.script_before_deploy"
                />

                <flux:textarea
                    name="form.script_after_deploy"
                    :label="__('Script to Run After Deploy')"
                    :description="__('This script will be executed just after updating the git repository during deployment.')"
                    class="font-mono"
                    rows="10"
                    wire:model="form.script_after_deploy"
                />

                <flux:textarea
                    name="form.script_before_activate"
                    :label="__('Script to Run Before Activating Release')"
                    :description="__('This script will be executed before activating the new deployment release (before swapping the symlink to the new release).')"
                    class="font-mono min-h-[220px]"
                    rows="10"
                    wire:model="form.script_before_activate"
                />

                <flux:textarea
                    name="form.script_after_activate"
                    :label="__('Script to Run After Activating Release')"
                    :description="__('This script will be executed after activating the new deployment release (after swapping the symlink to the new release).')"
                    class="font-mono min-h-[220px]"
                    rows="10"
                    wire:model="form.script_after_activate"
                />

                <flux:button type="submit" variant="primary">
                    {{ __('Save') }}
                </flux:button>
            </form>
        </div>
    </div>
</div>
