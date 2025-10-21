<?php

use App\Models\Cronjob;
use App\Models\Server;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Livewire\Volt\Volt;

use function Pest\Laravel\actingAs;

uses(RefreshDatabase::class);

it('allows an authorized user to delete a cronjob and dispatches the uninstall job', function () {
    Queue::fake();
    $server = Server::factory()->create();
    $user = $server->organization->user;
    $cronjob = Cronjob::factory()->for($server)->installed()->create();

    actingAs($user);

    Volt::test('servers.cronjobs.index', ['server' => $server])
        ->call('delete', $cronjob->id);

    $fresh = $cronjob->fresh();
    expect($fresh->status)->toBe('uninstalling');

    Queue::assertPushed(\App\Jobs\UninstallCronjobJob::class, function ($job) use ($cronjob) {
        return $job->cronjob->is($cronjob);
    });
});

it('prevents unauthorized users from deleting a cronjob', function () {
    $server = Server::factory()->create();
    $otherServer = Server::factory()->create();
    $user = $otherServer->organization->user;
    $cronjob = Cronjob::factory()->for($server)->installed()->create();

    actingAs($user);

    Volt::test('servers.cronjobs.index', ['server' => $otherServer])
        ->call('delete', $cronjob->id)
        ->assertForbidden();

    expect(Cronjob::find($cronjob->id))->not()->toBeNull();
});

it('does not dispatch uninstall job or change status if cronjob is already uninstalling', function () {
    Queue::fake();
    $server = Server::factory()->create();
    $user = $server->organization->user;
    $cronjob = Cronjob::factory()->for($server)->create(['status' => 'uninstalling']);

    actingAs($user);

    Volt::test('servers.cronjobs.index', ['server' => $server])
        ->call('delete', $cronjob->id);

    $fresh = $cronjob->fresh();
    expect($fresh->status)->toBe('uninstalling');
    Queue::assertNotPushed(\App\Jobs\UninstallCronjobJob::class);
});
