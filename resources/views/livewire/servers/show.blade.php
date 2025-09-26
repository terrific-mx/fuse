<?php

use App\Models\Server;
use Livewire\Volt\Component;

new class extends Component {
    public Server $server;
}; ?>

<div class="space-y-12">
    <flux:navbar scrollable>
        <flux:navbar.item :href="route('servers.show', $server)">Overview</flux:navbar.item>
        <flux:navbar.item :href="route('servers.sites.index', $server)">Sites</flux:navbar.item>
        <flux:navbar.item href="#">Databases</flux:navbar.item>
        <flux:navbar.item href="#">Cronjobs</flux:navbar.item>
        <flux:navbar.item href="#">Deamons</flux:navbar.item>
        <flux:navbar.item href="#">Firewall Rules</flux:navbar.item>
        <flux:navbar.item href="#">Backups</flux:navbar.item>
        <flux:navbar.item href="#">Software</flux:navbar.item>
        <flux:navbar.item href="#">Software</flux:navbar.item>
        <flux:navbar.item href="#">Files</flux:navbar.item>
        <flux:navbar.item href="#">Logs</flux:navbar.item>
    </flux:navbar>

    <section class="space-y-6">
        <flux:heading size="lg">{{ __('Server Overview') }}</flux:heading>

        <flux:input :label="__('Name')" value="{{ $server->name }}" readonly variant="filled" />

        <flux:input :label="__('Ip Address')" value="{{ $server->ip_address }}" readonly variant="filled" />

        <flux:input :label="__('Provider')" value="{{ $server->provider->name }} ({{ $server->provider->type }})" readonly variant="filled" />
    </section>

    <flux:separator class="my-12" />

    <section class="space-y-6">
        <flux:heading size="lg">{{ __('Delete Server') }}</flux:heading>

        <flux:button variant="filled">{{ __('Delete') }}</flux:button>
    </section>
</div>
