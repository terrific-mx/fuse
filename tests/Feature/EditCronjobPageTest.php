<?php

use App\Models\Cronjob;
use App\Models\Server;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Livewire\Volt\Volt;

use function Pest\Laravel\actingAs;

uses(RefreshDatabase::class);

it('allows a user to view the edit cronjob page for a server', function () {
    $server = Server::factory()->create();
    $cronjob = Cronjob::factory()->for($server)->create();
    $user = $server->organization->user;

    actingAs($user);

    Volt::test('servers.cronjobs.edit', ['server' => $server, 'cronjob' => $cronjob])
        ->assertOk();
});

it('updates a cronjob for a server', function () {
    Queue::fake();
    $server = Server::factory()->create();
    $cronjob = Cronjob::factory()->for($server)->create([
        'command' => 'php artisan schedule:run',
        'user' => 'root',
        'frequency' => 'hourly',
        'custom_expression' => null,
    ]);
    $user = $server->organization->user;

    actingAs($user);

    Volt::test('servers.cronjobs.edit', ['server' => $server, 'cronjob' => $cronjob])
        ->set('command', 'php artisan queue:work')
        ->set('user', 'ubuntu')
        ->set('frequency', 'daily')
        ->call('update')
        ->assertHasNoErrors();

    $cronjob->refresh();
    $cronjob->install();

    expect($cronjob->command)->toBe('php artisan queue:work');
    expect($cronjob->user)->toBe('ubuntu');
    expect($cronjob->frequency)->toBe('daily');

    Queue::assertPushed(\App\Jobs\InstallCronjobJob::class, function ($job) use ($cronjob) {
        return $job->cronjob->is($cronjob);
    });
});

it('validates required fields when updating a cronjob', function () {
    Queue::fake();
    $server = Server::factory()->create();
    $cronjob = Cronjob::factory()->for($server)->create();
    $user = $server->organization->user;

    actingAs($user);

    Volt::test('servers.cronjobs.edit', ['server' => $server, 'cronjob' => $cronjob])
        ->set('command', '')
        ->set('user', '')
        ->set('frequency', '')
        ->call('update')
        ->assertHasErrors(['command', 'user', 'frequency']);
});

it('allows updating a custom cron expression', function () {
    Queue::fake();
    $server = Server::factory()->create();
    $cronjob = Cronjob::factory()->for($server)->create([
        'frequency' => 'custom',
        'custom_expression' => '0 0 * * *',
    ]);
    $user = $server->organization->user;

    actingAs($user);

    Volt::test('servers.cronjobs.edit', ['server' => $server, 'cronjob' => $cronjob])
        ->set('frequency', 'custom')
        ->set('custom_expression', '*/15 * * * *')
        ->call('update')
        ->assertHasNoErrors();

    $cronjob->refresh();
    $cronjob->install();
    expect($cronjob->custom_expression)->toBe('*/15 * * * *');
    Queue::assertPushed(\App\Jobs\InstallCronjobJob::class, function ($job) use ($cronjob) {
        return $job->cronjob->is($cronjob);
    });
});

it('validates custom expression is required when frequency is custom on update', function () {
    Queue::fake();
    $server = Server::factory()->create();
    $cronjob = Cronjob::factory()->for($server)->create([
        'frequency' => 'custom',
        'custom_expression' => '0 0 * * *',
    ]);
    $user = $server->organization->user;

    actingAs($user);

    Volt::test('servers.cronjobs.edit', ['server' => $server, 'cronjob' => $cronjob])
        ->set('frequency', 'custom')
        ->set('custom_expression', '')
        ->call('update')
        ->assertHasErrors(['custom_expression']);
});
