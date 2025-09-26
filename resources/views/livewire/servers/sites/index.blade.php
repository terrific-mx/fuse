<?php

use App\Models\Server;
use Livewire\Volt\Component;

new class extends Component {
    public Server $server;
}; ?>

<div class="space-y-12">
    @include('partials.server-navbar', ['server' => $server])

    <section class="space-y-6">
        <flux:heading size="lg">{{ __('Add site') }}</flux:heading>

        <flux:input :label="__('Hostname')" />

        <flux:select :label="__('PHP version')" placeholder="Choose PHP version...">
            <flux:select.option>8.4</flux:select.option>
            <flux:select.option>8.3</flux:select.option>
            <flux:select.option>8.1</flux:select.option>
        </flux:select>

        <flux:select :label="__('Site type')" placeholder="Choose site type...">
            <flux:select.option>Generic</flux:select.option>
            <flux:select.option>Laravel</flux:select.option>
            <flux:select.option>Static</flux:select.option>
            <flux:select.option>Wordpress</flux:select.option>
        </flux:select>

        <flux:input :label="__('Web folder')" value="/public" />

        <flux:checkbox label="Enable Zero Downtime Deployment" checked />

        <flux:fieldset>
            <flux:legend>Repository</flux:legend>

            <div class="space-y-6">
                <flux:input :label="__('Repository URL')" value="git@github.com:laravel/laravel.git" />

                <flux:input :label="__('Repository Branch')" value="main" />
            </div>
        </flux:fieldset>

        <flux:switch label="Use a deploy key" />

        <flux:button variant="primary">{{ __('Add Site') }}</flux:button>
    </section>

    <section class="space-y-6">
        <flux:heading size="lg">{{ __('Sites') }}</flux:heading>

        <flux:table>
            <flux:table.columns>
                <flux:table.column>{{ __('Name') }}</flux:table.column>
                <flux:table.column>{{ __('Hostname') }}</flux:table.column>
                <flux:table.column>{{ __('Type') }}</flux:table.column>
                <flux:table.column>{{ __('Status') }}</flux:table.column>
            </flux:table.columns>
            <flux:table.rows>
                <flux:table.row>
                    <flux:table.cell>Laravel App</flux:table.cell>
                    <flux:table.cell>laravel.example.com</flux:table.cell>
                    <flux:table.cell>Laravel</flux:table.cell>
                    <flux:table.cell><flux:badge color="green" size="sm" inset="top bottom">Active</flux:badge></flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell>Wordpress Blog</flux:table.cell>
                    <flux:table.cell>blog.example.com</flux:table.cell>
                    <flux:table.cell>Wordpress</flux:table.cell>
                    <flux:table.cell><flux:badge color="zinc" size="sm" inset="top bottom">Inactive</flux:badge></flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell>Static Site</flux:table.cell>
                    <flux:table.cell>static.example.com</flux:table.cell>
                    <flux:table.cell>Static</flux:table.cell>
                    <flux:table.cell><flux:badge color="green" size="sm" inset="top bottom">Active</flux:badge></flux:table.cell>
                </flux:table.row>
            </flux:table.rows>
        </flux:table>
    </section>
</div>
