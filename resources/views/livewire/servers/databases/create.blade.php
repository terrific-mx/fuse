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
    }
}; ?>

<div>
    <form wire:submit="create" class="space-y-6 max-w-lg mx-auto mt-12">
        <flux:heading size="lg">Create Database</flux:heading>

        <div>
            <flux:input
                label="Database Name"
                name="name"
                wire:model="name"
                required
                autofocus
            />
        </div>

        <div>
            <flux:button type="submit">Create</flux:button>
        </div>
    </form>
</div>
