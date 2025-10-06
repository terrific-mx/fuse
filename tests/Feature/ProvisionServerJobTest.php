<?php

use App\Jobs\ProvisionServer;
use App\Models\Server;
use Illuminate\Support\Carbon;

it('calls provision on the server when the job runs', function () {
    $server = Server::factory()->create(['status' => 'pending']);

    $mock = Mockery::mock($server);
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
