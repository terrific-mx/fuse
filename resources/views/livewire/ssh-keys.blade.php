<?php

use Livewire\Volt\Component;

new class extends Component {
    //
}; ?>

<div>
    <form class="space-y-6">
        <flux:input
            label="{{ __('SSH Key Name') }}"
            name="ssh_key_name"
            required
        />
        <flux:input
            label="{{ __('Public Key') }}"
            name="public_key"
            required
        />
        <flux:button type="submit" variant="primary">
            {{ __('Add SSH Key') }}
        </flux:button>
    </form>

    <div class="mt-10">
        <flux:table>
            <flux:table.columns>
                <flux:table.column>{{ __('Name') }}</flux:table.column>
                <flux:table.column>{{ __('Public Key') }}</flux:table.column>
                <flux:table.column>{{ __('Created At') }}</flux:table.column>
            </flux:table.columns>
            <flux:table.rows>
                <flux:table.row :key="1">
                    <flux:table.cell>Work Laptop</flux:table.cell>
                    <flux:table.cell>ssh-rsa AAAAB3NzaC1yc2EAAAABIwAAAQEArandomkey1</flux:table.cell>
                    <flux:table.cell>2025-09-28 10:00</flux:table.cell>
                </flux:table.row>
                <flux:table.row :key="2">
                    <flux:table.cell>Home Desktop</flux:table.cell>
                    <flux:table.cell>ssh-ed25519 AAAAC3NzaC1lZDI1NTE5AAAAIrandomkey2</flux:table.cell>
                    <flux:table.cell>2025-09-27 15:30</flux:table.cell>
                </flux:table.row>
                <flux:table.row :key="3">
                    <flux:table.cell>Server Key</flux:table.cell>
                    <flux:table.cell>ssh-rsa AAAAB3NzaC1yc2EAAAABIwAAAQEArandomkey3</flux:table.cell>
                    <flux:table.cell>2025-09-26 08:45</flux:table.cell>
                </flux:table.row>
            </flux:table.rows>
        </flux:table>
    </div>
</div>
