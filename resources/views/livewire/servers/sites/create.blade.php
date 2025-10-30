<?php

use App\Livewire\Forms\SiteForm;
use App\Models\Server;
use Livewire\Attributes\Title;
use Livewire\Volt\Component;

new #[Title('Add Site')] class extends Component
{
    public Server $server;

    public SiteForm $form;

    public function mount()
    {
        $this->authorize('view', $this->server);
    }

    public function save()
    {
        $this->form->store($this->server);

        return redirect()->route('servers.sites.index', $this->server);
    }
}; ?>

<div>
    <flux:breadcrumbs>
        <flux:breadcrumbs.item :href="route('servers.index')" wire:navigate>Servers</flux:breadcrumbs.item>
        <flux:breadcrumbs.item :href="route('servers.show', $server)" wire:navigate>
            {{ $server->name }}
        </flux:breadcrumbs.item>
        <flux:breadcrumbs.item :href="route('servers.sites.index', $server)" wire:navigate>Sites</flux:breadcrumbs.item>
        <flux:breadcrumbs.item>Add Site</flux:breadcrumbs.item>
    </flux:breadcrumbs>

    <flux:spacer class="mt-8" />

    <form wire:submit="save" class="space-y-6 max-w-lg mx-auto">
        <flux:heading size="xl">Add site</flux:heading>

        <flux:input wire:model="form.hostname" label="Hostname" required />

        <flux:select wire:model="form.php_version" label="PHP version" required>
            <flux:select.option></flux:select.option>
            <flux:select.option value="8.4">8.4</flux:select.option>
            <flux:select.option value="8.3">8.3</flux:select.option>
            <flux:select.option value="8.2">8.2</flux:select.option>
            <flux:select.option value="8.1">8.1</flux:select.option>
        </flux:select>

        <flux:fieldset>
            <flux:legend class="text-sm">Repository</flux:legend>
            <div class="space-y-6">
                <flux:callout variant="secondary">
                    <flux:callout.heading icon="information-circle">
                        Repository access required
                    </flux:callout.heading>
                    <flux:callout.text>
                        To deploy code from your repository, add this server’s public SSH key as an access key to
                        your repository provider (e.g., GitHub, GitLab). This grants the server read access to your
                        repository so it can fetch and deploy your code.
                    </flux:callout.text>
                    <x-slot name="actions">
                        <flux:input icon="key" value="{{ $server->public_ssh_key }}" readonly copyable />
                    </x-slot>
                </flux:callout>
                <flux:input wire:model="form.repository_url" label="Repository URL" />
                <flux:input wire:model="form.repository_branch" label="Repository Branch" />
            </div>
        </flux:fieldset>

        <div class="flex">
            <flux:spacer />
            <flux:button :href="route('servers.sites.index', $server)" variant="ghost" wire:navigate>Cancel</flux:button>
            <flux:button type="submit" variant="primary">Add Site</flux:button>
        </div>
    </form>
</div>
