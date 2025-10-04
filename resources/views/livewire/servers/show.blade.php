<?php

use App\Models\Server;
use Livewire\Volt\Component;

new class extends Component {
    public Server $server;

    public function mount()
    {
        $this->authorize('view', $this->server);
    }
}; ?>

<x-slot:breadcrumbs>
    @include('partials.server-breadcrumbs', ['server' => $server, 'current' => __('Overview')])
</x-slot:breadcrumbs>

<div>
    @include('partials.server-heading')
    <div class="flex items-start max-md:flex-col">
        @include('partials.server-navbar', ['server' => $server])
        <flux:separator class="md:hidden" />
        <div class="flex-1 self-stretch max-md:pt-6">
            <section class="space-y-6 max-w-lg">
                <header>
                    <flux:heading>{{ __('Server Overview') }}</flux:heading>
                    <flux:text class="mt-2">{{ __('View details and credentials for this server.') }}</flux:text>
                </header>
                <flux:input :label="__('Name')" value="{{ $server->name }}" variant="filled" readonly />
                <flux:input :label="__('Ip Address')" value="{{ $server->ip_address }}" variant="filled" readonly />
                <flux:input :label="__('Sudo Password')" value="{{ $server->sudo_password }}" type="password" variant="filled" readonly viewable copyable />
                <flux:input :label="__('Database Password')" value="{{ $server->database_password }}" type="password" variant="filled" readonly viewable copyable />
            </section>
        </div>
    </div>
</div>
