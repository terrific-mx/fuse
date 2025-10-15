<?php

use App\Models\Server;
use Livewire\Volt\Component;

new class extends Component
{
    public Server $server;

    public string $command = '';

    public ?string $directory = null;

    public string $user = '';

    public int $processes = 1;

    public int $stop_wait_seconds = 5;

    public string $stop_signal = '';

    public function create()
    {
        $this->validate([
            'command' => ['required', 'string'],
            'directory' => ['nullable', 'string'],
            'user' => ['required', 'string'],
            'processes' => ['required', 'integer', 'min:1'],
            'stop_wait_seconds' => ['required', 'integer', 'min:0'],
            'stop_signal' => ['required', 'string'],
        ]);

        $this->server->daemons()->create([
            'command' => $this->command,
            'directory' => $this->directory,
            'user' => $this->user,
            'processes' => $this->processes,
            'stop_wait_seconds' => $this->stop_wait_seconds,
            'stop_signal' => $this->stop_signal,
        ]);

        session()->flash('success', 'Daemon created successfully');
        $this->reset(['command', 'directory', 'user', 'processes', 'stop_wait_seconds', 'stop_signal']);
    }
}; ?>

<div>
    <header>
        <flux:heading size="xl">{{ $server->name }}</flux:heading>
        @include('partials.server-navbar')
    </header>

    @if (session()->has('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <form wire:submit="create">
        <div>
            <label>Command</label>
            <input type="text" wire:model="command" required />
        </div>
        <div>
            <label>Directory (optional)</label>
            <input type="text" wire:model="directory" />
        </div>
        <div>
            <label>User</label>
            <input type="text" wire:model="user" required />
        </div>
        <div>
            <label>Processes</label>
            <input type="number" wire:model="processes" min="1" required />
        </div>
        <div>
            <label>Stop Wait Seconds</label>
            <input type="number" wire:model="stop_wait_seconds" min="0" required />
        </div>
        <div>
            <label>Stop Signal</label>
            <input type="text" wire:model="stop_signal" required />
        </div>
        <button type="submit">Create Daemon</button>
    </form>
</div>
