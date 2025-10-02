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

        <flux:input :label="__('Name')" value="{{ $server->name }}" variant="filled" readonly />

        <flux:input :label="__('Ip Address')" value="{{ $server->ip_address }}" variant="filled" readonly />

        <flux:input :label="__('Sudo Password')" value="{{ $server->sudo_password }}" type="password" variant="filled" readonly viewable copyable />

        <flux:input :label="__('Database Password')" value="{{ $server->database_password }}" type="password" variant="filled" readonly viewable copyable />
    </section>

    <flux:separator class="my-12" />

    <section class="space-y-6">
        <flux:heading size="lg">{{ __('Delete Server') }}</flux:heading>

        <flux:button variant="filled">{{ __('Delete') }}</flux:button>
    </section>
</div>
