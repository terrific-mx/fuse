<?php

use App\Models\Server;
use Livewire\Attributes\Title;
use Livewire\Volt\Component;

new #[Title('Create cronjob')] class extends Component
{
    public Server $server;

    public string $command = '';

    public string $user = '';

    public string $frequency = '';

    public string $custom_expression = '';

    public function mount()
    {
        $this->authorize('view', $this->server);
    }

    public function create()
    {
        $this->authorize('view', $this->server);

        $this->validate([
            'command' => ['required', 'string', 'max:255'],
            'user' => ['required', 'string', 'max:255'],
            'frequency' => ['required', 'string'],
            'custom_expression' => ['required_if:frequency,custom', 'nullable', 'string', 'max:255'],
        ]);

        $cronjob = $this->server->cronjobs()->create([
            'command' => $this->command,
            'user' => $this->user,
            'frequency' => $this->frequency,
            'custom_expression' => $this->frequency === 'custom' ? $this->custom_expression : null,
        ]);

        $cronjob->install();

        return redirect()->route('servers.cronjobs.index', $this->server);
    }
}; ?>

<div class="mx-auto max-w-[512px]">
    <flux:link
        :href="route('servers.cronjobs.index', $server)"
        class="inline-flex items-center gap-2 text-sm"
        variant="subtle"
        inline
        wire:navigate
    >
        <flux:icon.chevron-left variant="micro" />
        Cronjobs
    </flux:link>

    <flux:spacer class="mt-4 lg:mt-8" />

    <form wire:submit="create">
        <flux:heading class="text-xl">Create cronjob</flux:heading>

        <flux:spacer class="mt-10" />

        <div class="space-y-6">
            <flux:field>
                <flux:input wire:model="command" label="Command" required autofocus />
                <flux:error name="command" />
            </flux:field>
            <flux:field>
                <flux:input wire:model="user" label="User" required />
                <flux:error name="user" />
            </flux:field>
            <flux:field>
                <flux:select wire:model.live="frequency" label="Frequency" required placeholder="Select frequency...">
                    <flux:select.option value="every_minute">Every minute</flux:select.option>
                    <flux:select.option value="every_5_minutes">Every 5 minutes</flux:select.option>
                    <flux:select.option value="hourly">Hourly</flux:select.option>
                    <flux:select.option value="daily">Daily</flux:select.option>
                    <flux:select.option value="weekly">Weekly</flux:select.option>
                    <flux:select.option value="monthly">Monthly</flux:select.option>
                    <flux:select.option value="on_reboot">On reboot</flux:select.option>
                    <flux:select.option value="custom">Custom</flux:select.option>
                </flux:select>
                <flux:error name="frequency" />
            </flux:field>
            @if ($frequency === 'custom')
                <flux:field>
                    <flux:input wire:model="custom_expression" label="Custom expression (e.g. */7 * * * *)" />
                    <flux:error name="custom_expression" />
                </flux:field>
            @endif
        </div>

        <flux:spacer class="mt-8" />

        <div class="flex flex-col gap-4">
            <flux:button type="submit" variant="primary" color="zinc" class="w-full">Create cronjob</flux:button>
        </div>
    </form>
</div>
