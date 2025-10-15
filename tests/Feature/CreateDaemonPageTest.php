<?php

use App\Models\Server;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Volt\Volt;

use function Pest\Laravel\actingAs;

uses(RefreshDatabase::class);

it('allows a user to create a daemon for a server', function () {
    $server = Server::factory()->create();
    $user = $server->organization->user;

    actingAs($user);

    Volt::test('servers.daemons.create', ['server' => $server])
        ->set('command', 'php artisan queue:work')
        ->set('directory', '/var/www/app')
        ->set('user', 'forge')
        ->set('processes', 2)
        ->set('stop_wait_seconds', 10)
        ->set('stop_signal', 'SIGTERM')
        ->call('create')
        ->assertHasNoErrors()
        ->assertSee('Daemon created successfully');

    $daemon = $server->daemons()->first();

    expect($daemon)->not->toBeNull();
    expect($daemon->command)->toBe('php artisan queue:work');
    expect($daemon->directory)->toBe('/var/www/app');
    expect($daemon->user)->toBe('forge');
    expect($daemon->processes)->toBe(2);
    expect($daemon->stop_wait_seconds)->toBe(10);
    expect($daemon->stop_signal)->toBe('SIGTERM');
});
