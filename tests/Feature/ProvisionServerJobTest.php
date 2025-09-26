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

    $job->handle();

    expect($server->fresh())->toBeNull(); // Server should be deleted
})->throws(Exception::class, 'Server too old for provisioning');

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

it('retrieves the ip address from the server provider if missing when checking readiness', function () {
    $server = Server::factory()->create([
        'status' => 'pending',
        'ip_address' => null,
    ]);

    (new ProvisionServer($server))->handle();

    $server->refresh();
    expect($server->ip_address)->toBe('192.0.2.1'); // Should match stub value from HetznerCloudClient
});

todo('to check we can access the server we must run a get current directory command and verify is the root path');

todo('if we have server access we need to check apt is not locked');
