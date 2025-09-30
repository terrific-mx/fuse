<?php

use App\Jobs\ProvisionServer;
use App\Models\Server;

it('calls provision on the server when the job runs', function () {
    $server = Server::factory()->create(['status' => 'pending']);

    $mock = Mockery::mock($server);
    $mock->shouldReceive('provision')->once()->andReturnNull();

    (new ProvisionServer($mock))->handle();
});
