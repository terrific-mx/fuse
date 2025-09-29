<?php

use App\Models\Database;
use App\Models\Server;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Volt\Volt;

uses(RefreshDatabase::class);

it('creates a database for a server without a user', function () {
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

it('creates a database for a server with a user', function () {
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

it('adds a database user to a server and assigns database access', function () {
    $server = Server::factory()->create();
    $db1 = Database::factory()->for($server)->create(['name' => 'app_db']);
    $db2 = Database::factory()->for($server)->create(['name' => 'blog_db']);

    $component = Volt::actingAs($server->organization->user)
        ->test('servers.databases', ['server' => $server])
        ->set('userForm.name', 'new_user')
        ->set('userForm.password', 'super-secret')
        ->set('userForm.databases', [$db1->id, $db2->id])
        ->call('addUser');

    $component->assertHasNoErrors();

    $server->refresh();
    $user = $server->databaseUsers()->where('name', 'new_user')->first();
    expect($user)->not->toBeNull();
    expect($user->password)->not->toBeEmpty();
    expect($user->server_id)->toBe($server->id);
    expect($user->databases)->toHaveCount(2);
    expect($user->databases->pluck('id')->sort()->values()->toArray())->toEqual([$db1->id, $db2->id]);
});
