<?php

use App\Models\Server;
use Livewire\Volt\Volt;

test('users can add a cronjob to a server', function () {
    $server = Server::factory()->create();

    $component = Volt::actingAs($server->organization->user)
        ->test('servers.cronjobs', ['server' => $server])
        ->set('form.command', 'php artisan schedule:run')
        ->set('form.user', 'root')
        ->set('form.expression', '* * * * *')
        ->call('save');

    $component->assertHasNoErrors();

    $server->refresh();
    expect($server->cronjobs)->toHaveCount(1);

    $cronjob = $server->cronjobs()->first();
    expect($cronjob->command)->toBe('php artisan schedule:run');
    expect($cronjob->user)->toBe('root');
    expect($cronjob->expression)->toBe('* * * * *');
});
