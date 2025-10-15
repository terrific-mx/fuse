<?php

use App\Models\Server;
use Flux\Flux;
use Livewire\Volt\Component;

new class extends Component
{
    public Server $server;

    public string $command = '';

    public ?string $directory = null;

    public string $user = '';

    public int $processes = 1;

    public int $stop_wait_seconds = 5;

    public string $stop_signal = 'TERM';

    public array $signals = [
        'HUP', 'INT', 'QUIT', 'ILL', 'TRAP', 'ABRT', 'EMT', 'FPE', 'KILL', 'BUS', 'SEGV', 'SYS', 'PIPE', 'ALRM', 'TERM', 'URG', 'STOP', 'TSTP', 'CONT', 'CHLD', 'TTIN', 'TTOU', 'IO', 'XCPU', 'XFSZ', 'VTALRM', 'PROF', 'WINCH', 'INFO', 'USR1', 'USR2',
    ];

    public function create()
    {
        $this->validate([
            'command' => ['required', 'string'],
            'directory' => ['nullable', 'string'],
            'user' => ['required', 'string'],
            'processes' => ['required', 'integer', 'min:1'],
            'stop_wait_seconds' => ['required', 'integer', 'min:0'],
            'stop_signal' => ['required', 'string', 'in:'.implode(',', $this->signals)],
        ]);

        $this->server->daemons()->create([
            'command' => $this->command,
            'directory' => $this->directory,
            'user' => $this->user,
            'processes' => $this->processes,
            'stop_wait_seconds' => $this->stop_wait_seconds,
            'stop_signal' => $this->stop_signal,
        ]);

        Flux::toast([
            'heading' => 'Success!',
            'text' => 'Daemon created successfully',
            'variant' => 'success',
        ]);
        $this->reset(['command', 'directory', 'user', 'processes', 'stop_wait_seconds', 'stop_signal']);
    }
}; ?>

<div>
    <header>
        <flux:heading size="xl">{{ $server->name }}</flux:heading>
        @include('partials.server-navbar')
    </header>

    <form wire:submit="create" class="space-y-4 mt-8">
        <flux:input wire:model="command" label="Command" required />
        <flux:input wire:model="directory" label="Directory (optional)" />
        <flux:input wire:model="user" label="User" required />
        <flux:input wire:model="processes" label="Processes" type="number" min="1" required />
        <flux:input wire:model="stop_wait_seconds" label="Stop Wait Seconds" type="number" min="0" required />
        <flux:select wire:model="stop_signal" label="Stop Signal" required placeholder="Select a signal">
    @foreach ($signals as $signal)
        <flux:select.option value="{{ $signal }}">{{ $signal }}</flux:select.option>
    @endforeach
</flux:select>
        <flux:button type="submit" variant="primary">Create Daemon</flux:button>
    </form>
</div>
