<?php

use App\Jobs\InstallCronjobJob;
use App\Models\Server;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Livewire\Volt\Volt;

use function Pest\Laravel\actingAs;

uses(RefreshDatabase::class);

it('allows a user to view the create cronjob page for a server', function () {
    $server = Server::factory()->create();
    $user = $server->organization->user;

    actingAs($user);

    Volt::test('servers.cronjobs.create', ['server' => $server])
        ->assertOk();
});

it('creates a cronjob for a server', function () {
    Queue::fake();
    Queue::fake();
    $server = Server::factory()->create();
    $user = $server->organization->user;

    actingAs($user);

    Volt::test('servers.cronjobs.create', ['server' => $server])
        ->set('command', 'php artisan schedule:run')
        ->set('user', 'root')
        ->set('frequency', 'hourly')
        ->call('create')
        ->assertHasNoErrors();

    $cronjob = $server->cronjobs()->first();

    expect($cronjob)->not->toBeNull();
    expect($cronjob->command)->toBe('php artisan schedule:run');
    expect($cronjob->user)->toBe('root');
    expect($cronjob->frequency)->toBe('hourly');
    expect($cronjob->status)->toBe('installing');

    Queue::assertPushed(InstallCronjobJob::class, function ($job) use ($cronjob) {
        return $job->cronjob->is($cronjob);
    });
});

it('validates required fields when creating a cronjob', function () {
    $server = Server::factory()->create();
    $user = $server->organization->user;

    actingAs($user);

    Volt::test('servers.cronjobs.create', ['server' => $server])
        ->set('command', '')
        ->set('user', '')
        ->set('frequency', '')
        ->call('create')
        ->assertHasErrors(['command', 'user', 'frequency']);
});

it('allows a custom cron expression for frequency', function () {
    Queue::fake();
    $server = Server::factory()->create();
    $user = $server->organization->user;

    actingAs($user);

    Volt::test('servers.cronjobs.create', ['server' => $server])
        ->set('command', 'php artisan schedule:run')
        ->set('user', 'root')
        ->set('frequency', 'custom')
        ->set('custom_expression', '*/7 * * * *')
        ->call('create')
        ->assertHasNoErrors();

    $cronjob = $server->cronjobs()->first();

    expect($cronjob)->not->toBeNull();
    expect($cronjob->frequency)->toBe('custom');
    expect($cronjob->custom_expression)->toBe('*/7 * * * *');
    expect($cronjob->status)->toBe('installing');
    Queue::assertPushed(InstallCronjobJob::class, function ($job) use ($cronjob) {
        return $job->cronjob->is($cronjob);
    });
});

it('validates custom expression is required when frequency is custom', function () {
    $server = Server::factory()->create();
    $user = $server->organization->user;

    actingAs($user);

    Volt::test('servers.cronjobs.create', ['server' => $server])
        ->set('command', 'php artisan schedule:run')
        ->set('user', 'root')
        ->set('frequency', 'custom')
        ->set('custom_expression', '')
        ->call('create')
        ->assertHasErrors(['custom_expression']);
});
