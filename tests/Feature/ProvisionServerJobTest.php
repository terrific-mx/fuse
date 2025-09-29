<?php

use App\Jobs\ProvisionServer;
use App\Models\Server;

it('marks server as provisioning and creates a task when job runs', function () {
    $server = Server::factory()->create(['status' => 'pending']);

    (new ProvisionServer($server))->handle();

    $server->refresh();
    expect($server->status)->toBe('provisioning');

    $server->refresh();
    expect($server->tasks)->toHaveCount(1);
});
