<?php

use App\Livewire\Forms\UpdateDeploymentSettingsForm;
use App\Models\Server;
use App\Models\Site;
use Livewire\Volt\Component;

new class extends Component {
    public Server $server;

    public Site $site;

    public UpdateDeploymentSettingsForm $form;

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
            <section class="space-y-6 max-w-lg">
                <header>
                    <flux:heading></flux:heading>
                    <flux:text class="mt-2"></flux:text>
                </header>
                 <flux:textarea
                    name="form.shared_directories"
                    :label="__('Shared Directories')"
                    :placeholder="__('One directory per line')"
                    rows="3"
                    wire:model.defer="form.shared_directories"
                />

                <flux:textarea
                    name="form.shared_files"
                    :label="__('Shared Files')"
                    :placeholder="__('One file per line')"
                    rows="3"
                    wire:model.defer="form.shared_files"
                />

                <flux:textarea
                    name="form.writable_directories"
                    :label="__('Writable Directories')"
                    :placeholder="__('One directory per line')"
                    rows="3"
                    wire:model.defer="form.writable_directories"
                />

                <flux:textarea
                    name="form.script_before_deploy"
                    :label="__('Script Before Deploy')"
                    :placeholder="__('Enter script to run before deploy')"
                    rows="3"
                    wire:model.defer="form.script_before_deploy"
                />

                <flux:textarea
                    name="form.script_after_deploy"
                    :label="__('Script After Deploy')"
                    :placeholder="__('Enter script to run after deploy')"
                    rows="3"
                    wire:model.defer="form.script_after_deploy"
                />

                <flux:textarea
                    name="form.script_before_activate"
                    :label="__('Script Before Activate')"
                    :placeholder="__('Enter script to run before activate')"
                    rows="3"
                    wire:model.defer="form.script_before_activate"
                />

                <flux:textarea
                    name="form.script_after_activate"
                    :label="__('Script After Activate')"
                    :placeholder="__('Enter script to run after activate')"
                    rows="3"
                    wire:model.defer="form.script_after_activate"
                />

                <flux:button type="submit" class="w-full">
                    {{ __('Save') }}
                </flux:button>
            </section>
        </div>
    </div>
</div>
