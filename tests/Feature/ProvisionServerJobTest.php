<?php

use App\Jobs\ProvisionServer;
use App\Models\Server;

it('marks server as provisioning when job runs', function () {
    $server = Server::factory()->create(['status' => 'pending']);

    (new ProvisionServer($server))->handle();

    $server->refresh();
    expect($server->status)->toBe('provisioning');
});

it('does not update status if already provisioning', function () {
    $server = Server::factory()->create(['status' => 'provisioning']);

    $job = new ProvisionServer($server);
    $job->withFakeQueueInteractions();
    $job->handle();
    $job->assertReleased(delay: 30);

    $server->refresh();
    expect($server->status)->toBe('provisioning');
});

it('does not update status if server is not ready for provisioning', function () {
    $server = Server::factory()->create(['status' => 'pending']);

    $mock = Mockery::mock($server)->makePartial();
    $mock->shouldReceive('isReadyForProvisioning')->andReturn(false);

    $job = new ProvisionServer($mock);
    $job->withFakeQueueInteractions();

    $job->handle();

    $job->assertReleased(delay: 30);

    $mock->refresh();
    expect($mock->status)->toBe('pending');
});

it('fails the job if server is older than 15 minutes', function () {
    $server = Server::factory()->create([
        'status' => 'pending',
        'created_at' => now()->subMinutes(16),
    ]);

    $job = new ProvisionServer($server);
    $job->withFakeQueueInteractions();
    try {
        $job->handle();
    } catch (\Exception $e) {
        // Simulate job failure lifecycle
        $job->failed($e);
    }

    expect(Server::find($server->id))->toBeNull(); // Server should be deleted
});

it('deletes the job if server is provisioned', function () {
    $server = Server::factory()->create([
        'status' => 'provisioned',
    ]);

    $job = new ProvisionServer($server);
    $job->withFakeQueueInteractions();
    $job->handle();

    $job->assertDeleted();

    $server->refresh();
    expect($server->status)->toBe('provisioned');
});
