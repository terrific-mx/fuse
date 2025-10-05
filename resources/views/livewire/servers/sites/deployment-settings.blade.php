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
                <flux:heading></flux:heading>
                <flux:text class="mt-2"></flux:text>
            </header>
            <form wire:submit="save" class="max-w-lg space-y-6 mt-6">
                <flux:textarea
                    name="form.shared_directories"
                    :label="__('Shared Directories')"
                    :placeholder="__('One directory per line')"
                    class="font-mono"
                    rows="3"
                    wire:model="form.shared_directories"
                />

                <flux:textarea
                    name="form.shared_files"
                    :label="__('Shared Files')"
                    :placeholder="__('One file per line')"
                    class="font-mono"
                    rows="3"
                    wire:model="form.shared_files"
                />

                <flux:textarea
                    name="form.writable_directories"
                    :label="__('Writable Directories')"
                    :placeholder="__('One directory per line')"
                    class="font-mono"
                    rows="10"
                    wire:model="form.writable_directories"
                />

                <flux:textarea
                    name="form.script_before_deploy"
                    :label="__('Script Before Deploy')"
                    :placeholder="__('Enter script to run before deploy')"
                    class="font-mono min-h-[220px]"
                    rows="10"
                    wire:model="form.script_before_deploy"
                />

                <flux:textarea
                    name="form.script_after_deploy"
                    :label="__('Script After Deploy')"
                    :placeholder="__('Enter script to run after deploy')"
                    class="font-mono"
                    rows="10"
                    wire:model="form.script_after_deploy"
                />

                <flux:textarea
                    name="form.script_before_activate"
                    :label="__('Script Before Activate')"
                    :placeholder="__('Enter script to run before activate')"
                    class="font-mono min-h-[220px]"
                    rows="10"
                    wire:model="form.script_before_activate"
                />

                <flux:textarea
                    name="form.script_after_activate"
                    :label="__('Script After Activate')"
                    :placeholder="__('Enter script to run after activate')"
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
