<?php

use App\Models\Server;
use Livewire\Volt\Component;

use App\Livewire\Forms\FirewallRuleForm;

new class extends Component {
    public Server $server;

    public FirewallRuleForm $form;

    public function save()
    {
        $this->form->store($this->server);
    }
}; ?>

<div class="space-y-12">
    @include('partials.server-navbar', ['server' => $server])

    <section class="space-y-6">
        <header>
            <flux:heading size="lg">{{ __('Add firewall rule') }}</flux:heading>
            <flux:text class="mt-2">{{ __('Create a new firewall rule for your server.') }}</flux:text>
        </header>

        <form wire:submit="save" class="space-y-6">
            <flux:input :label="__('Name')" wire:model.defer="form.name" />

            <div class="flex">
                <flux:radio.group label="{{ __('Action') }}" variant="segmented" wire:model.defer="form.action">
                    <flux:radio value="allow" label="{{ __('Allow') }}" />
                    <flux:radio value="deny" label="{{ __('Deny') }}" />
                    <flux:radio value="reject" label="{{ __('Reject') }}" />
                </flux:radio.group>
            </div>

            <flux:input :label="__('Port')" type="number" min="1" max="65535" wire:model.defer="form.port" />
            <flux:input :label="__('From IP')" :badge="__('Optional')" wire:model.defer="form.from_ip" />

            <flux:button variant="primary" type="submit">{{ __('Add Rule') }}</flux:button>
        </form>
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
                @foreach ($server->firewallRules as $rule)
                    <flux:table.row>
                        <flux:table.cell>{{ $rule->name }}</flux:table.cell>
                        <flux:table.cell>{{ $rule->action }}</flux:table.cell>
                        <flux:table.cell>{{ $rule->port }}</flux:table.cell>
                        <flux:table.cell>{{ $rule->from_ip }}</flux:table.cell>
                        <flux:table.cell align="end">
                            <flux:button variant="ghost" size="sm" icon="ellipsis-horizontal" inset="top bottom"></flux:button>
                        </flux:table.cell>
                    </flux:table.row>
                @endforeach
            </flux:table.rows>
        </flux:table>
    </section>
</div>
