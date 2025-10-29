<?php

use App\Jobs\InstallDatabaseJob;
use App\Models\Server;
use Illuminate\Validation\Rule;
use Livewire\Volt\Component;

new class extends Component
{
    public Server $server;

    public string $name = '';

    public function mount()
    {
        $this->authorize('view', $this->server);
    }

    public function create()
    {
        $this->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('databases')->where(fn ($q) => $q->where('server_id', $this->server->id)),
            ],
        ]);

        $database = $this->server->databases()->create([
            'name' => $this->name,
        ]);

        dispatch(new InstallDatabaseJob($database));

        $this->redirectRoute('servers.databases.index', $this->server, navigate: true);
    }
}; ?>
<div>
    <flux:breadcrumbs>
        <flux:breadcrumbs.item :href="route('servers.show', $server)" wire:navigate>
            {{ $server->name }}
        </flux:breadcrumbs.item>

        <flux:breadcrumbs.item :href="route('servers.databases.index', $server)" wire:navigate>
            Databases
        </flux:breadcrumbs.item>
    </flux:breadcrumbs>

    <flux:spacer class="mt-8" />

    <form wire:submit="create">
        <flux:heading size="xl">Create Database</flux:heading>

        <flux:separator class="my-10 mt-6" />

        <section class="grid gap-x-8 gap-y-6 sm:grid-cols-2">
            <div>
                <flux:label>Database Name</flux:label>
            </div>
            <div>
                <flux:input wire:model="name" required autofocus />
                <flux:error name="name" />
            </div>
        </section>

        <flux:separator variant="subtle" class="my-10" />

        <div class="flex justify-end gap-4">
            <flux:button :href="route('servers.databases.index', $server)" variant="ghost" wire:navigate>Cancel</flux:button>
            <flux:button type="submit" variant="primary" color="zinc">Create Database</flux:button>
        </div>
    </form>
</div>
