<?php

use App\Models\Server;
use App\Models\SshKey;
use App\Scripts\ProvisionServer;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('it can render the script view with user ssh keys', function () {
    $server = Server::factory()->create();
    SshKey::factory()->for($server->user)->create(['public_key' => 'ssh-rsa test-public-key']);

    $script = new ProvisionServer($server);

    expect((string) $script->script())
        ->toContain('Add SSH keys to authorized_keys')
        ->toContain('ssh-rsa test-public-key');
});
