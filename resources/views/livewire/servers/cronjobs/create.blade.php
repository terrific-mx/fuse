<?php

use App\Models\Server;
use Livewire\Volt\Component;

new class extends Component
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
    }
}; ?>

<div>
    <form wire:submit="create" class="mx-auto mt-12 max-w-lg space-y-6">
        <flux:heading size="lg">Create Cronjob</flux:heading>

        <div>
            <flux:input label="Command" name="command" wire:model="command" required autofocus />
        </div>

        <div>
            <flux:input label="User" name="user" wire:model="user" required />
        </div>

        <div>
            <flux:select
                label="Frequency"
                name="frequency"
                wire:model="frequency"
                required
                placeholder="Select frequency..."
            >
                <flux:select.option value="every_minute">Every minute</flux:select.option>
                <flux:select.option value="every_5_minutes">Every 5 minutes</flux:select.option>
                <flux:select.option value="hourly">Hourly</flux:select.option>
                <flux:select.option value="daily">Daily</flux:select.option>
                <flux:select.option value="weekly">Weekly</flux:select.option>
                <flux:select.option value="monthly">Monthly</flux:select.option>
                <flux:select.option value="on_reboot">On reboot</flux:select.option>
                <flux:select.option value="custom">Custom</flux:select.option>
            </flux:select>
        </div>

        {{-- Show custom expression input if frequency is custom --}}
        <div>
            <flux:input
                label="Custom Expression"
                name="custom_expression"
                wire:model="custom_expression"
                placeholder="e.g. */7 * * * *"
            />
        </div>

        <div>
            <flux:button type="submit">Create</flux:button>
        </div>
    </form>
</div>
