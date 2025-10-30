<?php

use App\Jobs\InstallDaemonJob;
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

    public function mount()
    {
        $this->authorize('view', $this->server);
    }

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

        $daemon = $this->server->daemons()->create([
            'command' => $this->command,
            'directory' => $this->directory,
            'user' => $this->user,
            'processes' => $this->processes,
            'stop_wait_seconds' => $this->stop_wait_seconds,
            'stop_signal' => $this->stop_signal,
        ]);

        dispatch(new InstallDaemonJob($daemon));

        $this->redirectRoute('servers.daemons.index', $this->server, navigate: true);
    }
}; ?>

<div>
    <flux:breadcrumbs>
        <flux:breadcrumbs.item :href="route('servers.index', $server)" wire:navigate>Servers</flux:breadcrumbs.item>
        <flux:breadcrumbs.item :href="route('servers.show', $server)" wire:navigate>
            {{ $server->name }}
        </flux:breadcrumbs.item>
        <flux:breadcrumbs.item :href="route('servers.daemons.index', $server)" wire:navigate>
            Daemons
        </flux:breadcrumbs.item>
    </flux:breadcrumbs>

    <flux:spacer class="mt-8" />

    <form wire:submit="create">
        <flux:heading size="xl">Create Daemon</flux:heading>

        <flux:separator class="my-10 mt-6" />

        <section class="grid gap-x-8 gap-y-6 sm:grid-cols-2">
            <div>
                <flux:heading>Daemon details</flux:heading>
            </div>
            <div class="space-y-6">
                <flux:field>
                    <flux:input wire:model="command" placeholder="Command" required autofocus />
                    <flux:error name="command" />
                </flux:field>

                <flux:field>
                    <flux:input wire:model="directory" placeholder="Directory (optional)" />
                    <flux:error name="directory" />
                </flux:field>

                <flux:field>
                    <flux:input wire:model="user" placeholder="User" required />
                    <flux:error name="user" />
                </flux:field>
            </div>
        </section>

        <flux:separator variant="subtle" class="my-10" />

        <section class="grid gap-x-8 gap-y-6 sm:grid-cols-2">
            <div>
                <flux:heading>Process settings</flux:heading>
            </div>
            <div class="space-y-6">
                <flux:field>
                    <flux:input wire:model="processes" placeholder="Processes" type="number" min="1" required />
                    <flux:error name="processes" />
                </flux:field>

                <flux:field>
                    <flux:input
                        wire:model="stop_wait_seconds"
                        placeholder="Stop Wait Seconds"
                        type="number"
                        min="0"
                        required
                    />
                    <flux:error name="stop_wait_seconds" />
                </flux:field>

                <flux:field>
                    <flux:select wire:model.live="stop_signal" required placeholder="Select a signal...">
                        @foreach ($signals as $signal)
                            <flux:select.option value="{{ $signal }}">{{ $signal }}</flux:select.option>
                        @endforeach
                    </flux:select>
                    <flux:error name="stop_signal" />
                </flux:field>
            </div>
        </section>

        <flux:separator variant="subtle" class="my-10" />

        <div class="flex justify-end gap-4">
            <flux:button :href="route('servers.daemons.index', $server)" variant="ghost" wire:navigate>
                Cancel
            </flux:button>
            <flux:button type="submit" variant="primary" color="zinc">Create Daemon</flux:button>
        </div>
    </form>
</div>
