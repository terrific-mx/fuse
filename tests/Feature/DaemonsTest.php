<?php

use App\Models\Server;
use Livewire\Volt\Volt;

// TDD: users can add a daemon to a server

test('users can add a daemon to a server', function () {
    $server = Server::factory()->create();

    $component = Volt::actingAs($server->organization->user)
        ->test('servers.daemons', ['server' => $server])
        ->set('form.command', 'php artisan queue:work')
        ->set('form.directory', '/var/www')
        ->set('form.user', 'www-data')
        ->set('form.processes', 2)
        ->set('form.stop_wait', 10)
        ->set('form.stop_signal', 'SIGTERM')
        ->call('save');

    $component->assertHasNoErrors();

    $server->refresh();
    expect($server->daemons)->toHaveCount(1);

    $daemon = $server->daemons()->first();
    expect($daemon->command)->toBe('php artisan queue:work');
    expect($daemon->directory)->toBe('/var/www');
    expect($daemon->user)->toBe('www-data');
    expect($daemon->processes)->toBe(2);
    expect($daemon->stop_wait)->toBe(10);
    expect($daemon->stop_signal)->toBe('SIGTERM');
});
