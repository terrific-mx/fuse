<?php

use App\Models\SshKey;
use App\Scripts\AddAuthorizedKey;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('can render the script view to add the ssh key', function () {
    /** @var SshKey */
    $sshKey = SshKey::factory()->create();

    $script = new AddAuthorizedKey($sshKey);

    expect((string) $script->script())
        ->toContain('fuse')
        ->toContain($sshKey->public_key);
});
