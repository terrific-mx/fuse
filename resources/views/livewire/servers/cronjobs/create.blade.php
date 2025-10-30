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

<div>
    <flux:breadcrumbs>
        <flux:breadcrumbs.item :href="route('servers.index', $server)" wire:navigate>Servers</flux:breadcrumbs.item>
        <flux:breadcrumbs.item :href="route('servers.show', $server)" wire:navigate>
            {{ $server->name }}
        </flux:breadcrumbs.item>
        <flux:breadcrumbs.item :href="route('servers.cronjobs.index', $server)" wire:navigate>
            Cronjobs
        </flux:breadcrumbs.item>
    </flux:breadcrumbs>

    <flux:spacer class="mt-8" />

    <form wire:submit="create">
        <flux:heading size="xl">Create Cronjob</flux:heading>

        <flux:separator class="my-10 mt-6" />

        <section class="grid gap-x-8 gap-y-6 sm:grid-cols-2">
            <div>
                <flux:heading>Cronjob details</flux:heading>
            </div>
            <div class="space-y-6">
                <flux:field>
                    <flux:input wire:model="command" placeholder="Command" required autofocus />
                    <flux:error name="command" />
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
                <flux:heading>Schedule</flux:heading>
            </div>
            <div class="space-y-6">
                <flux:field>
                    <flux:select wire:model.live="frequency" required placeholder="Select frequency...">
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
                        <flux:input wire:model="custom_expression" placeholder="e.g. */7 * * * *" />
                        <flux:error name="custom_expression" />
                    </flux:field>
                @endif
            </div>
        </section>

        <flux:separator variant="subtle" class="my-10" />

        <div class="flex justify-end gap-4">
            <flux:button :href="route('servers.cronjobs.index', $server)" variant="ghost" wire:navigate>
                Cancel
            </flux:button>
            <flux:button type="submit" variant="primary" color="zinc">Create Cronjob</flux:button>
        </div>
    </form>
</div>
