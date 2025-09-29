<?php

use App\Models\Server;
use App\Callbacks\MarkServerProvisioned;

it('marks a server as provisioned', function () {
    $server = Server::factory()->create(['status' => 'pending']);
    $callback = new MarkServerProvisioned();

    $callback($server);

    expect($server->fresh()->status)->toBe('provisioned');
});
