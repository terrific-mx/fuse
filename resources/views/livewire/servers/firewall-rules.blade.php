<?php

use App\Models\Server;
use Livewire\Volt\Component;

new class extends Component {
    public Server $server;
}; ?>

<div class="space-y-12">
    @include('partials.server-navbar', ['server' => $server])

    <section class="space-y-6">
        <header>
            <flux:heading size="lg">{{ __('Add firewall rule') }}</flux:heading>
            <flux:text class="mt-2">{{ __('Create a new firewall rule for your server.') }}</flux:text>
        </header>

        <flux:input :label="__('Name')" />

        <div class="flex">
            <flux:radio.group label="{{ __('Action') }}" variant="segmented">
                <flux:radio value="allow" label="{{ __('Allow') }}" checked />
                <flux:radio value="deny" label="{{ __('Deny') }}" />
                <flux:radio value="reject" label="{{ __('Reject') }}" />
            </flux:radio.group>
        </div>

        <flux:input :label="__('Port')" type="number" min="1" max="65535" />
        <flux:input :label="__('From IP')" :badge="__('Optional')" />

        <flux:button variant="primary">{{ __('Add Rule') }}</flux:button>
    </section>

    <section class="space-y-6">
        <header>
            <flux:heading size="lg">{{ __('Firewall Rules') }}</flux:heading>
            <flux:text class="mt-2">{{ __('List of firewall rules on this server.') }}</flux:text>
        </header>

        <flux:table>
            <flux:table.columns>
                <flux:table.column>{{ __('Name') }}</flux:table.column>
                <flux:table.column>{{ __('Action') }}</flux:table.column>
                <flux:table.column>{{ __('Port') }}</flux:table.column>
                <flux:table.column>{{ __('From IP') }}</flux:table.column>
            </flux:table.columns>
            <flux:table.rows>
                <flux:table.row>
                    <flux:table.cell>SSH</flux:table.cell>
                    <flux:table.cell>allow</flux:table.cell>
                    <flux:table.cell>22</flux:table.cell>
                    <flux:table.cell>0.0.0.0/0</flux:table.cell>
                    <flux:table.cell align="end">
                        <flux:button variant="ghost" size="sm" icon="ellipsis-horizontal" inset="top bottom"></flux:button>
                    </flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell>HTTP</flux:table.cell>
                    <flux:table.cell>allow</flux:table.cell>
                    <flux:table.cell>80</flux:table.cell>
                    <flux:table.cell>0.0.0.0/0</flux:table.cell>
                    <flux:table.cell align="end">
                        <flux:button variant="ghost" size="sm" icon="ellipsis-horizontal" inset="top bottom"></flux:button>
                    </flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell>Admin Panel</flux:table.cell>
                    <flux:table.cell>deny</flux:table.cell>
                    <flux:table.cell>8080</flux:table.cell>
                    <flux:table.cell>192.168.1.0/24</flux:table.cell>
                    <flux:table.cell align="end">
                        <flux:button variant="ghost" size="sm" icon="ellipsis-horizontal" inset="top bottom"></flux:button>
                    </flux:table.cell>
                </flux:table.row>
            </flux:table.rows>
        </flux:table>
    </section>
</div>
