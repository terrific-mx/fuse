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
    <flux:breadcrumbs>
        <flux:breadcrumbs.item :href="route('ssh-keys.index')" wire:navigate>
            SSH Keys
        </flux:breadcrumbs.item>
    </flux:breadcrumbs>

    <flux:spacer class="mt-8" />

    <form wire:submit="assignServers">
        <div class="flex items-center justify-between">
            <flux:heading size="xl">{{ $sshKey->name }}</flux:heading>
            <div class="flex gap-2">
                <flux:dropdown align="end">
                    <flux:button icon:trailing="chevron-down">
                        Delete
                    </flux:button>
                    <flux:menu>
                        <flux:menu.item
                            wire:click="delete"
                            wire:confirm="Are you sure you want to delete this SSH key? This will remove it from all servers."
                            variant="danger"
                        >
                            Delete & remove from servers
                        </flux:menu.item>
                        <flux:menu.separator />
                        <flux:menu.item
                            wire:click="purge"
                            wire:confirm="Delete SSH key from database only? It will remain on all associated servers."
                            variant="danger"
                        >
                            Delete only (keep on servers)
                        </flux:menu.item>
                    </flux:menu>
                </flux:dropdown>
            </div>
        </div>

        <flux:separator class="my-10 mt-6" />

        <section class="grid gap-x-8 gap-y-6 sm:grid-cols-2">
            <div>
                <flux:label>Server Access</flux:label>
            </div>
            <div>
                <flux:checkbox.group wire:model="selectedServers">
                    <flux:checkbox.all label="Select all" />
                    @foreach ($servers as $server)
                        <flux:checkbox label="{{ $server->name }}" value="{{ $server->id }}" />
                    @endforeach
                </flux:checkbox.group>
                <flux:error name="selectedServers" />
            </div>
        </section>

        <flux:separator variant="subtle" class="my-10" />

        <div class="flex justify-end gap-4">
            <flux:button :href="route('ssh-keys.index')" variant="ghost" wire:navigate>Cancel</flux:button>
            <flux:button type="submit" variant="primary" color="zinc">Save</flux:button>
        </div>
    </form>
</div>
