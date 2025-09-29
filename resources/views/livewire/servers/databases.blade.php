<?php

use App\Models\Server;
use Livewire\Volt\Component;

use App\Livewire\Forms\DatabaseForm;
use App\Livewire\Forms\DatabaseUserForm;
use App\Models\DatabaseUser;

use Livewire\Attributes\Computed;

new class extends Component {
    public Server $server;

    public DatabaseForm $form;
    public DatabaseUserForm $userForm;

    public function save()
    {
        $this->form->store($this->server);
    }

    public function addUser()
    {
        $this->userForm->store($this->server);
    }


    #[Computed]
    public function databases()
    {
        return $this->server->databases()->with('users')->orderByDesc('created_at')->get();
    }

    #[Computed]
    public function databaseUsers()
    {
        return $this->server->databaseUsers()->with('databases')->orderByDesc('created_at')->get();
    }
}; ?>

<div class="space-y-12">
    @include('partials.server-navbar', ['server' => $server])

    <section class="space-y-6" x-data="{ createUser: false }">
        <header>
            <flux:heading size="lg">{{ __('Add database') }}</flux:heading>
            <flux:text class="mt-2">{{ __('Create a new database for your server.') }}</flux:text>
        </header>

        <form wire:submit="save" class="space-y-6">
            <flux:input :label="__('Database name')" wire:model="form.name" required />

            <flux:switch :label="__('Create database user')" wire:model="form.create_user" />

            <div class="space-y-6" x-show="$wire.form.create_user" x-cloak>
                <flux:input :label="__('Database user name')" wire:model="form.user_name" />

                <flux:field>
                    <flux:label>{{ __('Password') }}</flux:label>
                    <flux:input.group>
                        <flux:input type="password" wire:model="form.password" />
                        <flux:button icon="sparkles">{{ __('Auto generate') }}</flux:button>
                    </flux:input.group>
                    <flux:error name="form.password" />
                </flux:field>
            </div>

            <flux:button type="submit" variant="primary">{{ __('Add Database') }}</flux:button>
        </form>
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
                @foreach ($this->databases as $database)
                    <flux:table.row :key="$database->id">
                        <flux:table.cell>{{ $database->name }}</flux:table.cell>
                        <flux:table.cell>
                            @if ($database->users->isNotEmpty())
                                {{ $database->users->pluck('name')->join(', ') }}
                            @else
                                <span class="text-zinc-400">{{ __('No users') }}</span>
                            @endif
                        </flux:table.cell>
                        <flux:table.cell>
                            <flux:badge color="green" size="sm" inset="top bottom">Active</flux:badge>
                        </flux:table.cell>
                    </flux:table.row>
                @endforeach
            </flux:table.rows>
        </flux:table>
    </section>

    <section class="space-y-6">
        <header>
            <flux:heading size="lg">{{ __('Add database user') }}</flux:heading>
            <flux:text class="mt-2">{{ __('Create a user and assign database access.') }}</flux:text>
        </header>

        <form wire:submit="addUser" class="space-y-6">
            <flux:input :label="__('User name')" wire:model="userForm.name" required />

            <flux:field>
                <flux:label>{{ __('Password') }}</flux:label>
                <flux:input.group>
                    <flux:input type="password" wire:model="userForm.password" required />
                    <flux:button icon="sparkles">{{ __('Auto generate') }}</flux:button>
                </flux:input.group>
            </flux:field>

            <flux:pillbox multiple placeholder="Select databases..." :label="__('Databases access')" wire:model="userForm.databases">
                @foreach ($this->databases as $database)
                    <flux:pillbox.option :value="$database->id">{{ $database->name }}</flux:pillbox.option>
                @endforeach
            </flux:pillbox>

            <flux:button type="submit" variant="primary">{{ __('Add User') }}</flux:button>
        </form>
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
                @foreach ($this->databaseUsers as $user)
                    <flux:table.row :key="$user->id">
                        <flux:table.cell>{{ $user->name }}</flux:table.cell>
                        <flux:table.cell>
                            @if ($user->databases->isNotEmpty())
                                {{ $user->databases->pluck('name')->join(', ') }}
                            @else
                                <span class="text-zinc-400">{{ __('No databases') }}</span>
                            @endif
                        </flux:table.cell>
                        <flux:table.cell>
                            <flux:badge color="green" size="sm" inset="top bottom">Active</flux:badge>
                        </flux:table.cell>
                    </flux:table.row>
                @endforeach
            </flux:table.rows>
        </flux:table>
    </section>
</div>
