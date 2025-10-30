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
    <flux:breadcrumbs>
        <flux:breadcrumbs.item :href="route('servers.index')" wire:navigate>Servers</flux:breadcrumbs.item>
        <flux:breadcrumbs.item :href="route('servers.show', $server)" wire:navigate>
            {{ $server->name }}
        </flux:breadcrumbs.item>
        <flux:breadcrumbs.item :href="route('servers.sites.index', $server)" wire:navigate>Sites</flux:breadcrumbs.item>
        <flux:breadcrumbs.item :href="route('servers.sites.show', [$server, $site])" wire:navigate>
            {{ $site->hostname }}
        </flux:breadcrumbs.item>
    </flux:breadcrumbs>

    <flux:spacer class="mt-8" />

    <form wire:submit="save">
        <flux:heading size="xl">Deployment Settings</flux:heading>

        <flux:separator class="my-10 mt-6" />

        <section class="grid gap-x-8 gap-y-6 sm:grid-cols-2">
            <div>
                <flux:heading>Shared Resources</flux:heading>
            </div>
            <div class="space-y-6">
                <flux:field>
                    <flux:textarea
                        name="form.shared_directories"
                        placeholder="e.g. storage, public/uploads – one per line"
                        class="font-mono"
                        rows="3"
                        wire:model="form.shared_directories"
                    />
                </flux:field>
                <flux:field>
                    <flux:textarea
                        name="form.shared_files"
                        placeholder="Files Shared Across Deployments"
                        class="font-mono"
                        rows="3"
                        wire:model="form.shared_files"
                    />
                </flux:field>
            </div>
        </section>

        <flux:separator variant="subtle" class="my-10" />

        <section class="grid gap-x-8 gap-y-6 sm:grid-cols-2">
            <div>
                <flux:heading>Writable Directories</flux:heading>
            </div>
            <div class="space-y-6">
                <flux:field>
                    <flux:textarea
                        name="form.writable_directories"
                        placeholder="e.g. storage, bootstrap/cache – one per line"
                        class="font-mono"
                        rows="10"
                        wire:model="form.writable_directories"
                    />
                </flux:field>
            </div>
        </section>

        <flux:separator variant="subtle" class="my-10" />

        <section class="grid gap-x-8 gap-y-6 sm:grid-cols-2">
            <div>
                <flux:heading>Deployment Scripts</flux:heading>
            </div>
            <div class="space-y-6">
                <flux:field>
                    <flux:textarea
                        name="form.script_before_deploy"
                        placeholder="Optional shell script to run before deployment (leave blank if not needed)"
                        class="min-h-[220px] font-mono"
                        rows="10"
                        wire:model="form.script_before_deploy"
                    />
                </flux:field>
                <flux:field>
                    <flux:textarea
                        name="form.script_after_deploy"
                        placeholder="Optional shell script to run after deployment (leave blank if not needed)"
                        class="font-mono"
                        rows="10"
                        wire:model="form.script_after_deploy"
                    />
                </flux:field>
                <flux:field>
                    <flux:textarea
                        name="form.script_before_activate"
                        placeholder="Optional shell script to run before activating the new release (leave blank if not needed)"
                        class="min-h-[220px] font-mono"
                        rows="10"
                        wire:model="form.script_before_activate"
                    />
                </flux:field>
                <flux:field>
                    <flux:textarea
                        name="form.script_after_activate"
                        placeholder="Optional shell script to run after activating the new release (leave blank if not needed)"
                        class="min-h-[220px] font-mono"
                        rows="10"
                        wire:model="form.script_after_activate"
                    />
                </flux:field>
            </div>
        </section>

        <flux:separator variant="subtle" class="my-10" />

        <div class="flex justify-end gap-4">
            <flux:button :href="route('servers.sites.show', [$server, $site])" variant="ghost" wire:navigate>
                Cancel
            </flux:button>
            <flux:button type="submit" variant="primary" color="zinc">Save</flux:button>
        </div>
    </form>
</div>
