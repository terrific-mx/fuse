<?php

use App\Jobs\ProvisionServer;
use App\Models\Server;

it('marks server as provisioning when job runs', function () {
    $server = Server::factory()->create(['status' => 'pending']);

    (new ProvisionServer($server))->handle();

    $server->refresh();
    expect($server->status)->toBe('provisioning');
});
