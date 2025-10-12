<?php

use App\Livewire\Forms\SiteForm;
use App\Models\Server;
use Livewire\Attributes\Computed;
use Livewire\Volt\Component;

new class extends Component
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
    }

    #[Computed]
    public function sites()
    {
        return $this->server->sites()->orderByDesc('created_at')->get();
    }
}; ?>

<div>
    <header>
        <flux:heading size="xl">{{ $server->name }}</flux:heading>
        @include('partials.server-navbar')
    </header>
    <section class="mt-8">
        <flux:modal.trigger name="add-site">
            <flux:button variant="primary">Add site</flux:button>
        </flux:modal.trigger>

        <div class="mt-4">
            <flux:table>
                <flux:table.columns>
                    <flux:table.column>Hostname</flux:table.column>
                    <flux:table.column>PHP version</flux:table.column>
                    <flux:table.column>Repository URL</flux:table.column>
                    <flux:table.column>Repository Branch</flux:table.column>
                </flux:table.columns>
                <flux:table.rows>
                    @foreach ($this->sites as $site)
                        <flux:table.row :key="$site->id">
                            <flux:table.cell><flux:link :href="route('servers.sites.show', ['server' => $server, 'site' => $site])" wire:navigate>{{ $site->hostname }}</flux:link></flux:table.cell>
                            <flux:table.cell>{{ $site->php_version }}</flux:table.cell>
                            <flux:table.cell>{{ $site->repository_url }}</flux:table.cell>
                            <flux:table.cell>{{ $site->repository_branch }}</flux:table.cell>
                        </flux:table.row>
                    @endforeach
                </flux:table.rows>
            </flux:table>
        </div>
    </section>
    <flux:modal name="add-site" variant="flyout" class="max-w-lg">
        <form wire:submit="save" class="space-y-6">
            <flux:heading size="lg">Add site</flux:heading>

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
                        <flux:callout.heading icon="information-circle">Repository access required</flux:callout.heading>
                        <flux:callout.text>To deploy code from your repository, add this server’s public SSH key as an access key to your repository provider (e.g., GitHub, GitLab). This grants the server read access to your repository so it can fetch and deploy your code.</flux:callout.text>
                        <x-slot name="actions">
                            <flux:input
                                icon="key"
                                value="{{ $server->public_ssh_key }}"
                                readonly
                                copyable
                            />
                        </x-slot>
                    </flux:callout>
                    <flux:input wire:model="form.repository_url" label="Repository URL" />
                    <flux:input wire:model="form.repository_branch" label="Repository Branch" />
                </div>
            </flux:fieldset>

            <div class="flex">
                <flux:spacer />
                <flux:button type="submit" variant="primary">Add Site</flux:button>
            </div>
        </form>
    </flux:modal>
</div>
