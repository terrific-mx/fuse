<?php

use App\Models\Server;
use Livewire\Volt\Component;

new class extends Component {
    public Server $server;
}; ?>

<div class="space-y-12">
    @include('partials.server-navbar', ['server' => $server])

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
