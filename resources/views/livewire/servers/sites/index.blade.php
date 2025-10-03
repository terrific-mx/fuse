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

<div class="space-y-12">
    @include('partials.server-navbar', ['server' => $server])

    <form wire:submit="save" class="space-y-6">
        <flux:heading size="lg">{{ __('Add site') }}</flux:heading>

        <flux:input :label="__('Hostname')" wire:model="form.hostname" required />

        <flux:select :label="__('PHP version')" wire:model="form.php_version" placeholder="Choose PHP version..." required>
            <flux:select.option value="8.4">8.4</flux:select.option>
            <flux:select.option value="8.3">8.3</flux:select.option>
            <flux:select.option value="8.1">8.1</flux:select.option>
        </flux:select>

        <flux:fieldset>
            <flux:legend>Repository</flux:legend>

            <div class="space-y-6">
                <flux:input :label="__('Repository URL')" wire:model="form.repository_url" />

                <flux:input :label="__('Repository Branch')" wire:model="form.repository_branch" />
            </div>
        </flux:fieldset>

        <flux:button type="submit" variant="primary">{{ __('Add Site') }}</flux:button>
    </form>

    <section class="space-y-6">
        <flux:heading size="lg">{{ __('Sites') }}</flux:heading>

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
    </section>
</div>
