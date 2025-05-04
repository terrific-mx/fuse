<?php

use App\Models\Server;
use App\Models\Site;
use App\Scripts\UpdateCaddyImports;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('can render the script with all installed sites', function () {
    $server = Server::factory()->create();
    $siteA = Site::factory()->for($server)->create(['domain' => 'foo.com', 'status' => 'installed']);
    $siteB = Site::factory()->for($server)->create(['domain' => 'bar.com', 'status' => 'installed']);
    $siteC = Site::factory()->for($server)->create(['domain' => 'baz.com', 'status' => 'creating']);
    $script = new UpdateCaddyImports($server);

    expect((string) $script)
        ->toContain('import /home/fuse/foo.com/Caddyfile')
        ->toContain('import /home/fuse/bar.com/Caddyfile')
        ->not()->toContain('import /home/fuse/baz.com/Caddyfile');
});
