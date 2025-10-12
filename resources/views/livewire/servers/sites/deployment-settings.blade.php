<?php

use App\Livewire\Forms\DeploymentSettingsForm;
use App\Models\Server;
use App\Models\Site;
use Flux\Flux;
use Livewire\Volt\Component;

new class extends Component
{
    public Server $server;

    public Site $site;

    public DeploymentSettingsForm $form;

    public function mount(): void
    {
        $this->authorize('view', $this->server);

        $this->authorize('update', $this->site);

        $this->form->setSite($this->site);
    }

    public function save(): void
    {
        $this->form->update();

        Flux::toast(
            heading: __('Saved'),
            text: __('Deployment settings updated successfully.'),
            variant: 'success'
        );
    }
}; ?>

<div>
    <header>
        <flux:breadcrumbs class="mb-2">
            <flux:breadcrumbs.item :href="route('servers.show', $server)" separator="slash" wire:navigate>{{ $server->name }}</flux:breadcrumbs.item>
        </flux:breadcrumbs>
        <flux:heading size="xl">{{ $site->hostname }}</flux:heading>
        @include('partials.site-navbar')
    </header>

    <form wire:submit="save" class="max-w-lg space-y-6 mt-8">
        <flux:textarea
            name="form.shared_directories"
            label="Directories Shared Across Deployments"

            class="font-mono"
            rows="3"
            wire:model="form.shared_directories"
        />

        <flux:textarea
            name="form.shared_files"
            label="Files Shared Across Deployments"

            class="font-mono"
            rows="3"
            wire:model="form.shared_files"
        />

        <flux:textarea
            name="form.writable_directories"
            label="Writable Directories for Webserver"

            class="font-mono"
            rows="10"
            wire:model="form.writable_directories"
        />

        <flux:textarea
            name="form.script_before_deploy"
            :label="__('Script to Run Before Deploy')"

            class="font-mono min-h-[220px]"
            rows="10"
            wire:model="form.script_before_deploy"
        />

        <flux:textarea
            name="form.script_after_deploy"
            :label="__('Script to Run After Deploy')"

            class="font-mono"
            rows="10"
            wire:model="form.script_after_deploy"
        />

        <flux:textarea
            name="form.script_before_activate"
            :label="__('Script to Run Before Activating Release')"

            class="font-mono min-h-[220px]"
            rows="10"
            wire:model="form.script_before_activate"
        />

        <flux:textarea
            name="form.script_after_activate"
            :label="__('Script to Run After Activating Release')"

            class="font-mono min-h-[220px]"
            rows="10"
            wire:model="form.script_after_activate"
        />

        <flux:button type="submit" variant="primary">
            {{ __('Save') }}
        </flux:button>
    </form>
</div>
