<?php

use App\Models\Server;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Volt\Volt;

uses(RefreshDatabase::class);

test('users can create a database for a server without a user', function () {
    $server = Server::factory()->create();

    $component = Volt::actingAs($server->organization->user)
        ->test('servers.databases', ['server' => $server])
        ->set('form.name', 'app_db')
        ->set('form.create_user', false)
        ->call('save');

    $component->assertHasNoErrors();

    $server->refresh();
    expect($server->databases)->toHaveCount(1);

    $database = $server->databases()->first();
    expect($database->name)->toBe('app_db');
    expect($database->users)->toHaveCount(0);
});

test('users can create a database for a server with a user', function () {
    $server = Server::factory()->create();

    $component = Volt::actingAs($server->organization->user)
        ->test('servers.databases', ['server' => $server])
        ->set('form.name', 'app_db')
        ->set('form.create_user', true)
        ->set('form.user_name', 'app_user')
        ->set('form.password', 'secret-password')
        ->call('save');

    $component->assertHasNoErrors();

    $server->refresh();
    expect($server->databases)->toHaveCount(1);

    $database = $server->databases()->first();
    expect($database->name)->toBe('app_db');
    expect($database->users)->toHaveCount(1);

    $user = $database->users()->first();
    expect($user->name)->toBe('app_user');
    expect($user->password)->not->toBeEmpty();
});
