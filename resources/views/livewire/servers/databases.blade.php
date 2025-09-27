<?php

use App\Models\Server;
use Livewire\Volt\Component;

new class extends Component {
    public Server $server;
}; ?>

<div class="space-y-12">
    @include('partials.server-navbar', ['server' => $server])

    <section class="space-y-6" x-data="{ createUser: false }">
        <header>
            <flux:heading size="lg">{{ __('Add database') }}</flux:heading>
            <flux:text class="mt-2">{{ __('Create a new database for your server.') }}</flux:text>
        </header>

        <flux:input :label="__('Database name')" />

        <flux:switch label="{{ __('Create database user') }}" x-model="createUser" />

        <div class="space-y-6" x-show="createUser">
            <flux:input :label="__('Database user name')" />

            <flux:field>
                <flux:label>{{ __('Password') }}</flux:label>
                <flux:input.group>
                    <flux:input type="password" />
                    <flux:button icon="sparkles">{{ __('Auto generate') }}</flux:button>
                </flux:input.group>
            </flux:field>
        </div>

        <flux:button variant="primary">{{ __('Add Database') }}</flux:button>
    </section>

    <section class="space-y-6">
        <header>
            <flux:heading size="lg">{{ __('Databases') }}</flux:heading>
            <flux:text class="mt-2">{{ __('List of databases on this server.') }}</flux:text>
        </header>

        <flux:table>
            <flux:table.columns>
                <flux:table.column>{{ __('Name') }}</flux:table.column>
                <flux:table.column>{{ __('Users') }}</flux:table.column>
                <flux:table.column>{{ __('Status') }}</flux:table.column>
            </flux:table.columns>
            <flux:table.rows>
                <flux:table.row>
                    <flux:table.cell>app_db</flux:table.cell>
                    <flux:table.cell>app_user</flux:table.cell>
                    <flux:table.cell><flux:badge color="green" size="sm" inset="top bottom">Active</flux:badge></flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell>blog_db</flux:table.cell>
                    <flux:table.cell>blog_user</flux:table.cell>
                    <flux:table.cell><flux:badge color="zinc" size="sm" inset="top bottom">Inactive</flux:badge></flux:table.cell>
                </flux:table.row>
            </flux:table.rows>
        </flux:table>
    </section>

    <section class="space-y-6">
        <header>
            <flux:heading size="lg">{{ __('Add database user') }}</flux:heading>
            <flux:text class="mt-2">{{ __('Create a user and assign database access.') }}</flux:text>
        </header>

        <flux:input :label="__('User name')" />

        <flux:field>
            <flux:label>{{ __('Password') }}</flux:label>
            <flux:input.group>
                <flux:input type="password" />
                <flux:button icon="sparkles">{{ __('Auto generate') }}</flux:button>
            </flux:input.group>
        </flux:field>

        <flux:pillbox multiple placeholder="Select databases..." :label="__('Databases access')">
            <flux:pillbox.option value="app_db">app_db</flux:pillbox.option>
            <flux:pillbox.option value="blog_db">blog_db</flux:pillbox.option>
            <flux:pillbox.option value="analytics_db">analytics_db</flux:pillbox.option>
        </flux:pillbox>

        <flux:button variant="primary">{{ __('Add User') }}</flux:button>
    </section>

    <section class="space-y-6">
        <header>
            <flux:heading size="lg">{{ __('Database users') }}</flux:heading>
            <flux:text class="mt-2">{{ __('List of users with database access.') }}</flux:text>
        </header>

        <flux:table>
            <flux:table.columns>
                <flux:table.column>{{ __('Name') }}</flux:table.column>
                <flux:table.column>{{ __('Databases') }}</flux:table.column>
                <flux:table.column>{{ __('Status') }}</flux:table.column>
            </flux:table.columns>
            <flux:table.rows>
                <flux:table.row>
                    <flux:table.cell>app_user</flux:table.cell>
                    <flux:table.cell>app_db</flux:table.cell>
                    <flux:table.cell><flux:badge color="green" size="sm" inset="top bottom">Active</flux:badge></flux:table.cell>
                </flux:table.row>
                <flux:table.row>
                    <flux:table.cell>blog_user</flux:table.cell>
                    <flux:table.cell>blog_db</flux:table.cell>
                    <flux:table.cell><flux:badge color="zinc" size="sm" inset="top bottom">Inactive</flux:badge></flux:table.cell>
                </flux:table.row>
            </flux:table.rows>
        </flux:table>
    </section>
</div>
