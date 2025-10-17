<?php

use App\Jobs\DeauthorizeSshKeyOnServerJob;
use App\Models\SshKey;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Computed;
use Livewire\Volt\Component;

new class extends Component
{
    public SshKey $sshKey;

    public array $selectedServers = [];

    public Collection $servers;

    #[Computed]
    public function organization()
    {
        return Auth::user()->currentOrganization;
    }

    public function mount()
    {
        $this->authorize('update', $this->sshKey);

        $this->servers = $this->organization->servers()->get();
        $this->selectedServers = $this->sshKey->servers()->pluck('id')->toArray();
    }

    public function assignServers()
    {
        $this->authorize('update', $this->sshKey);

        $this->validate([
            'selectedServers' => ['array'],
            'selectedServers.*' => [
                'required',
                Rule::exists('servers', 'id')
                    ->where('organization_id', $this->organization->id),
            ],
        ]);

        $this->sshKey->syncServers($this->servers->whereIn('id', $this->selectedServers));
        $this->sshKey->refresh();
    }

    public function delete()
    {
        $this->authorize('delete', $this->sshKey);

        $this->sshKey->servers->each(function ($server) {
            DeauthorizeSshKeyOnServerJob::dispatch($this->sshKey, $server);
        });

        $this->sshKey->delete();

        return redirect()->route('ssh-keys.index');
    }

    public function purge()
    {
        $this->authorize('delete', $this->sshKey);
        $this->sshKey->servers()->detach();
        $this->sshKey->delete();

        return redirect()->route('ssh-keys.index');
    }
}; ?>

<div>
    <header class="-mt-6 flex items-center lg:-mt-8">
        <flux:heading size="lg">SSH keys</flux:heading>
        <flux:spacer />
        <div class="flex items-center gap-4">
            <flux:navbar>
                <flux:navbar.item :href="route('ssh-keys.index')" :accent="false" wire:navigate>
                    Overview
                </flux:navbar.item>
            </flux:navbar>
            <flux:button :href="route('ssh-keys.create')" variant="primary" color="zinc" size="sm" wire:navigate>
                Add
            </flux:button>
        </div>
    </header>

    <flux:spacer class="mt-12" />

    <flux:breadcrumbs>
        <flux:breadcrumbs.item :href="route('ssh-keys.index')" wire:navigate>SSH keys</flux:breadcrumbs.item>
        <flux:breadcrumbs.item>{{ $sshKey->name }}</flux:breadcrumbs.item>
    </flux:breadcrumbs>

    <flux:spacer class="mt-3" />

    <flux:heading size="xl">{{ $sshKey->name }}</flux:heading>

    <form wire:submit="assignServers" class="mt-6 space-y-6">
        <flux:checkbox.group wire:model="selectedServers" label="Server Access">
            <flux:checkbox.all label="Select all" />
            @foreach ($servers as $server)
                <flux:checkbox label="{{ $server->name }}" value="{{ $server->id }}" />
            @endforeach
        </flux:checkbox.group>
        <flux:button type="submit" variant="primary" color="zinc">Save</flux:button>
    </form>

    <flux:spacer class="mt-10" />

    <div class="flex gap-2">
        <flux:button
            wire:click="delete"
            wire:confirm="Are you sure you want to delete this SSH key? This will remove it from all servers."
        >
            Delete & remove from servers
        </flux:button>
        <flux:button
            wire:click="purge"
            wire:confirm="Delete SSH key from database only? It will remain on all associated servers."
            variant="ghost"
        >
            Delete only (keep on servers)
        </flux:button>
    </div>
</div>
