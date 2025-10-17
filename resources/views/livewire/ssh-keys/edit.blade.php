<?php

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

    public function deleteSshKey()
    {
        $this->authorize('delete', $this->sshKey);

        foreach ($this->sshKey->servers as $server) {
            \App\Jobs\DeauthorizeSshKeyOnServerJob::dispatch($this->sshKey, $server);
        }

        $this->sshKey->delete();
        // Optionally, redirect or show a message here
    }
}; ?>

<div>
    <form wire:submit="assignServers">
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

    <form wire:submit="deleteSshKey" class="mt-4">
        <button type="submit" class="btn btn-danger"
            onclick="return confirm('Are you sure you want to delete this SSH key? This will remove it from all servers.');">
            Delete SSH Key
        </button>
    </form>
</div>
