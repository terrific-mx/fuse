<?php

use App\Jobs\UninstallDatabaseJob;
use App\Models\Database;
use App\Models\Server;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Livewire\Volt\Volt;

use function Pest\Laravel\actingAs;

uses(RefreshDatabase::class);

it('allows an authorized user to delete a database and dispatches the uninstall job', function () {
    Queue::fake();
    $server = Server::factory()->create();
    $user = $server->organization->user;
    $database = Database::factory()->for($server)->installed()->create();

    actingAs($user);

    Volt::test('servers.databases.index', ['server' => $server])
        ->call('delete', $database->id);

    $fresh = $database->fresh();
    expect($fresh->status)->toBe('deleting');

    Queue::assertPushed(UninstallDatabaseJob::class, function ($job) use ($database) {
        return $job->database->is($database);
    });
});

it('prevents unauthorized users from deleting a database', function () {
    $server = Server::factory()->create();
    $otherServer = Server::factory()->create();
    $user = $otherServer->organization->user;
    $database = Database::factory()->for($server)->installed()->create();

    actingAs($user);

    Volt::test('servers.databases.index', ['server' => $otherServer])
        ->call('delete', $database->id)
        ->assertForbidden();

    expect(Database::find($database->id))->not()->toBeNull();
});

it('does not dispatch uninstall job or change status if database is already deleting', function () {
    Queue::fake();
    $server = Server::factory()->create();
    $user = $server->organization->user;
    $database = Database::factory()->for($server)->create(['status' => 'deleting']);

    actingAs($user);

    Volt::test('servers.databases.index', ['server' => $server])
        ->call('delete', $database->id);

    $fresh = $database->fresh();
    expect($fresh->status)->toBe('deleting');
    Queue::assertNotPushed(UninstallDatabaseJob::class);
});
