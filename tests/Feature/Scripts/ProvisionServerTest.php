<?php

use App\Models\Server;
use App\Models\SshKey;
use App\Models\User;
use App\Scripts\ProvisionServer;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('it can render the script view with user ssh keys', function () {
    $user = User::factory()->create();
    $sshKey = SshKey::factory()->for($user)->create(['public_key' => 'ssh-rsa test-public-key']);
    $server = Server::factory()->for($user)->create();

    $script = new ProvisionServer($server);

    expect((string) $script->script())
        ->toContain('Add SSH keys to authorized_keys')
        ->toContain('ssh-rsa test-public-key');
});
