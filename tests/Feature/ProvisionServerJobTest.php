<?php

use App\Jobs\ProvisionServer;
use App\Models\Server;
use Illuminate\Foundation\Testing\RefreshDatabase;

it('marks server as provisioning when job runs', function () {
    $server = Server::factory()->create(['status' => 'pending']);

    (new ProvisionServer($server))->handle();

    $server->refresh();
    expect($server->status)->toBe('provisioning');
});

it('does not update status if already provisioning', function () {
    $server = Server::factory()->create(['status' => 'provisioning']);

    (new ProvisionServer($server))->handle();

    $server->refresh();
    expect($server->status)->toBe('provisioning');
});
