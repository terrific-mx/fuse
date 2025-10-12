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
}; ?>

<div>
    <header>
        <flux:breadcrumbs class="mb-2">
            <flux:breadcrumbs.item :href="route('servers.show', $server)" separator="slash" wire:navigate>{{ $server->name }}</flux:breadcrumbs.item>
        </flux:breadcrumbs>
        <flux:heading size="xl">{{ $site->hostname }}</flux:heading>
        @include('partials.site-navbar')
    </header>

    <flux:button wire:click="getEnvFile">
    {{ __('Show .env file') }}
</flux:button>

    @if ($envContent)
        <pre class="mt-4 p-4 bg-zinc-100 dark:bg-zinc-800 rounded text-xs overflow-x-auto">{{ $envContent }}</pre>
    @endif
</div>
