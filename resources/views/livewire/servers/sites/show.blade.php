<?php

use App\Models\Server;
use App\Models\Site;
use Livewire\Volt\Component;

new class extends Component
{
    public Server $server;

    public Site $site;

    public function mount()
    {
        $this->authorize('view', $this->site);
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
    <section class="space-y-6 max-w-lg mt-8">
        <flux:input label="Hostname" value="{{ $site->hostname }}" readonly variant="filled" />
        <flux:input label="PHP version" value="{{ $site->php_version }}" readonly variant="filled" />
        <flux:input label="Repository URL" value="{{ $site->repository_url }}" readonly variant="filled" />
        <flux:input label="Repository Branch" value="{{ $site->repository_branch }}" readonly variant="filled" />
    </section>
</div>
