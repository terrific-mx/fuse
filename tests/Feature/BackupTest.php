<?php

use App\Models\Server;
use Livewire\Volt\Volt;

test('users can add a backup to a server', function () {
    $server = Server::factory()->create();

    $component = Volt::actingAs($server->organization->user)
        ->test('servers.backups', ['server' => $server])
        ->set('form.name', 'Daily DB & Files')
        ->set('form.disk', 'local')
        ->set('form.databases', ['mysql', 'analytics'])
        ->set('form.directories', "/var/www/html\n/storage/uploads")
        ->set('form.retention', 7)
        ->set('form.frequency', '0 0 * * *')
        ->set('form.notify_failure', true)
        ->set('form.notify_success', false)
        ->set('form.notification_email', 'admin@example.com')
        ->call('save');

    $component->assertHasNoErrors();

    $server->refresh();
    expect($server->backups)->toHaveCount(1);

    $backup = $server->backups()->first();
    expect($backup->name)->toBe('Daily DB & Files');
    expect($backup->disk)->toBe('local');
    expect($backup->databases)->toBe(['mysql', 'analytics']);
    expect($backup->directories)->toBe(['/var/www/html', '/storage/uploads']);
    expect($backup->retention)->toBe(7);
    expect($backup->frequency)->toBe('0 0 * * *');
    expect($backup->notify_failure)->toBeTrue();
    expect($backup->notify_success)->toBeFalse();
    expect($backup->notification_email)->toBe('admin@example.com');
});
