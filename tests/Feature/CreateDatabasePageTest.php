<?php

use App\Models\Server;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Volt\Volt;

use function Pest\Laravel\actingAs;

uses(RefreshDatabase::class);

it('allows a user to view the create database page for a server', function () {
    $server = Server::factory()->create();
    $user = $server->organization->user;

    actingAs($user);

    Volt::test('servers.databases.create', ['server' => $server])
        ->assertOk();
});
