<?php

use App\Models\Server;
use App\Models\Application;
use App\Scripts\UpdateCaddyImports;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('can render the script with all installed applications', function () {
    $server = Server::factory()->create();
    $applicationA = Application::factory()->for($server)->create(['domain' => 'foo.com', 'status' => 'installed']);
    $applicationB = Application::factory()->for($server)->create(['domain' => 'bar.com', 'status' => 'installed']);
    $applicationC = Application::factory()->for($server)->create(['domain' => 'baz.com', 'status' => 'creating']);
    $script = new UpdateCaddyImports($server);

    expect((string) $script)
        ->toContain('import /home/fuse/foo.com/Caddyfile')
        ->toContain('import /home/fuse/bar.com/Caddyfile')
        ->not()->toContain('import /home/fuse/baz.com/Caddyfile');
});
