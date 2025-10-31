<?php

use App\Jobs\InstallDaemonJob;
use App\Models\Server;
use Livewire\Attributes\Title;
use Livewire\Volt\Component;

new #[Title('Create daemon')] class extends Component
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

<div class="mx-auto max-w-[512px]">
    <flux:link
        :href="route('servers.daemons.index', $server)"
        class="inline-flex items-center gap-2 text-sm"
        variant="subtle"
        inline
        wire:navigate
    >
        <flux:icon.chevron-left variant="micro" />
        Daemons
    </flux:link>

    <flux:spacer class="mt-4 lg:mt-8" />

    <form wire:submit="create">
        <flux:heading class="text-xl">Create daemon</flux:heading>

        <flux:spacer class="mt-10" />

        <div class="space-y-6">
            <flux:field>
                <flux:input wire:model="command" label="Command" required autofocus />
                <flux:error name="command" />
            </flux:field>
            <flux:field>
                <flux:input wire:model="directory" label="Directory (optional)" />
                <flux:error name="directory" />
            </flux:field>
            <flux:field>
                <flux:input wire:model="user" label="User" required />
                <flux:error name="user" />
            </flux:field>
            <flux:field>
                <flux:input wire:model="processes" label="Processes" type="number" min="1" required />
                <flux:error name="processes" />
            </flux:field>
            <flux:field>
                <flux:input wire:model="stop_wait_seconds" label="Stop wait seconds" type="number" min="0" required />
                <flux:error name="stop_wait_seconds" />
            </flux:field>
            <flux:field>
                <flux:select
                    wire:model.live="stop_signal"
                    label="Stop signal"
                    required
                    placeholder="Select a signal..."
                >
                    @foreach ($signals as $signal)
                        <flux:select.option value="{{ $signal }}">{{ $signal }}</flux:select.option>
                    @endforeach
                </flux:select>
                <flux:error name="stop_signal" />
            </flux:field>
        </div>

        <flux:spacer class="mt-8" />

        <div class="flex flex-col gap-4">
            <flux:button type="submit" variant="primary" color="zinc" class="w-full">Create daemon</flux:button>
        </div>
    </form>
</div>
