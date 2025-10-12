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
    <header>
        <flux:breadcrumbs class="mb-2">
            <flux:breadcrumbs.item :href="route('servers.show', $server)" separator="slash" wire:navigate>{{ $server->name }}</flux:breadcrumbs.item>
        </flux:breadcrumbs>
        <flux:heading size="xl">{{ $site->hostname }}</flux:heading>
        @include('partials.site-navbar')
    </header>

    <div class="flex gap-2 mt-4">
        <flux:button wire:click="getEnvFile">
            Reload .env file
        </flux:button>
        <flux:button wire:click="saveEnvFile" color="primary" :disabled="!$envContent">
            Save .env file
        </flux:button>
    </div>

    <div class="mt-4">
        <flux:textarea
            wire:model="envContent"
            rows="20"
            class="font-mono"
            placeholder="The .env file will appear here..."
        ></flux:textarea>
    </div>
</div>
