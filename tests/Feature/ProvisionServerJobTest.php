<?php

use App\Jobs\ProvisionServer;
use App\Models\Server;
use Illuminate\Support\Carbon;

it('calls provision on the server when the job runs', function () {
    $server = Server::factory()->create(['status' => 'pending']);

    $mock = Mockery::mock($server);
    $mock->shouldReceive('isReadyForProvisioning')->andReturn(true);
    $mock->shouldReceive('provision')->once()->andReturnNull();

    (new ProvisionServer($mock))->handle();
});

it('deletes the job if the server is already provisioned', function () {
    $server = Server::factory()->create(['status' => 'provisioned']);
    $job = (new ProvisionServer($server))->withFakeQueueInteractions();

    $job->handle();

    $job->assertDeleted();
});

it('fails the job if the server is older than 15 minutes', function () {
    $server = Server::factory()->create([
        'status' => 'pending',
        'created_at' => Carbon::now()->subMinutes(16),
    ]);
    $job = (new ProvisionServer($server))->withFakeQueueInteractions();

    $job->handle();

    $job->assertFailed();
});

it('releases the job for 30 seconds if the server is still provisioning', function () {
    $server = Server::factory()->create([
        'status' => 'provisioning',
    ]);
    $job = (new ProvisionServer($server))->withFakeQueueInteractions();

    $job->handle();

    $job->assertReleased(30);
});

it('deletes the server when the job fails', function () {
    $server = Server::factory()->create();
    $job = (new ProvisionServer($server))->withFakeQueueInteractions();

    $job->failed(new Exception('Simulated failure'));

    expect($server->fresh())->toBeNull();
});

it('does not provision the server if it is not ready for provisioning', function () {
    $server = Server::factory()->create(['status' => 'pending']);
    $mock = Mockery::mock($server);
    $mock->shouldReceive('isReadyForProvisioning')->andReturn(false);
    $mock->shouldReceive('provision')->never();
    $job = (new ProvisionServer($mock))->withFakeQueueInteractions();

    $job->handle();

    $job->assertReleased(30);
});
