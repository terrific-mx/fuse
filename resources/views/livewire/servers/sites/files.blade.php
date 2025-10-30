<?php

use App\Models\Server;
use App\Models\Site;
use Livewire\Volt\Component;

new class extends Component
{
    public Server $server;

    public Site $site;

    public string $envContent = '';

    public function getEnvFile(): void
    {
        $this->envContent = $this->site->env();
    }

    public function saveEnvFile(): void
    {
        $this->site->setEnv($this->envContent);
    }
}; ?>

<div>
    <flux:breadcrumbs>
        <flux:breadcrumbs.item :href="route('servers.index')" wire:navigate>Servers</flux:breadcrumbs.item>
        <flux:breadcrumbs.item :href="route('servers.show', $server)" wire:navigate>
            {{ $server->name }}
        </flux:breadcrumbs.item>
        <flux:breadcrumbs.item :href="route('servers.sites.index', $server)" wire:navigate>Sites</flux:breadcrumbs.item>
        <flux:breadcrumbs.item :href="route('servers.sites.show', [$server, $site])" wire:navigate>
            {{ $site->hostname }}
        </flux:breadcrumbs.item>
    </flux:breadcrumbs>

    <flux:spacer class="mt-8" />

    <form wire:submit.prevent="saveEnvFile">
        <flux:heading size="xl">Environment File</flux:heading>

        <flux:separator class="my-10 mt-6" />

        <section class="grid gap-x-8 gap-y-6 sm:grid-cols-2">
            <div>
                <flux:heading>.env File</flux:heading>
                <p class="mt-2 text-sm text-zinc-500">
                    Edit the environment variables for this site. Be careful—changes take effect immediately.
                </p>
            </div>
            <div class="space-y-6">
                <flux:field>
                    <flux:textarea
                        wire:model="envContent"
                        rows="20"
                        class="font-mono"
                        placeholder="The .env file will appear here..."
                    />
                </flux:field>
            </div>
        </section>

        <flux:separator variant="subtle" class="my-10" />

        <div class="flex justify-end gap-4">
            <flux:button type="button" wire:click="getEnvFile" variant="ghost">Reload .env file</flux:button>
            <flux:button type="submit" color="primary" :disabled="!$envContent">Save .env file</flux:button>
        </div>
    </form>
</div>
