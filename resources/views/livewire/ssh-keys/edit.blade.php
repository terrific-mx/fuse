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
    <header class="flex items-center -mt-6 lg:-mt-8">
        <flux:heading size="lg">SSH keys</flux:heading>
        <flux:spacer />
        <flux:navbar>
            <flux:navbar.item :href="route('ssh-keys.index')" :accent="false" wire:navigate>
                Overview
            </flux:navbar.item>
            <flux:button :href="route('ssh-keys.create')" variant="primary" color="zinc" size="sm" wire:navigate>
                Add
            </flux:button>
        </flux:navbar>
    </header>
    <form wire:submit="assignServers" class="mt-12">
        <h2 class="text-lg font-bold mb-4">Assign Servers to SSH Key</h2>
        <div class="space-y-2 mb-6">
            @foreach ($servers as $server)
                <label class="flex items-center space-x-2">
                    <input type="checkbox" wire:model="selectedServers" value="{{ $server->id }}">
                    <span>{{ $server->name }}</span>
                </label>
            @endforeach
        </div>
        <button type="submit" class="btn btn-primary">Save</button>
    </form>

    <form wire:submit="delete" class="mt-4">
        <button type="submit" class="btn btn-danger"
            onclick="return confirm('Are you sure you want to delete this SSH key? This will remove it from all servers.');">
            Delete SSH Key
        </button>
    </form>

    <form wire:submit="purge" class="mt-2">
        <button type="submit" class="btn btn-warning"
            onclick="return confirm('Delete SSH key from database only? It will remain on all associated servers.');">
            Delete SSH Key Only (Keep on Servers)
        </button>
    </form>
</div>
