<?php

use App\Jobs\InstallDatabaseJob;
use App\Models\Server;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Title;
use Livewire\Volt\Component;

new #[Title('Create database')] class extends Component
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

<div class="max-w-[512px] mx-auto">
    <flux:link :href="route('servers.databases.index', $server)" class="inline-flex items-center gap-2 text-sm" variant="subtle" inline wire:navigate>
        <flux:icon.chevron-left variant="micro" />
        Databases
    </flux:link>

    <flux:spacer class="mt-4 lg:mt-8" />

    <form wire:submit="create">
        <flux:heading class="text-xl">Create database</flux:heading>

        <flux:spacer class="mt-10" />

        <div class="space-y-6">
            <flux:field>
                <flux:input wire:model="name" label="Database name" required autofocus />
                <flux:error name="name" />
            </flux:field>
        </div>

        <flux:spacer class="mt-8" />

        <div class="flex flex-col gap-4">
            <flux:button type="submit" variant="primary" color="zinc" class="w-full">Create database</flux:button>
        </div>
    </form>
</div>

