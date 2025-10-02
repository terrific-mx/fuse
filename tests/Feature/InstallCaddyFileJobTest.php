<?php

use App\Jobs\InstallCaddyFileJob;
use App\Models\Site;
use Illuminate\Support\Facades\Process;

it('creates server tasks to install the Caddy file and update Caddy sites for the site', function () {
    Process::fake();

    $site = Site::factory()->create();
    $server = $site->server;

    (new InstallCaddyFileJob($site))->handle();

    expect($server->tasks)->toHaveCount(2);

    $installTask = $server->tasks->firstWhere('name', 'install_caddy_file');
    expect($installTask)->not->toBeNull();
    expect($installTask->user)->toBe('fuse');
    expect($installTask->script)->not->toBeNull();
    expect($installTask->script)->not->toBe('');
    expect($installTask->status)->toBe('running');

    $updateTask = $server->tasks->firstWhere('name', 'update_caddy_sites');
    expect($updateTask)->not->toBeNull();
    expect($updateTask->user)->toBe('root');
    expect($updateTask->script)->not->toBeNull();
    expect($updateTask->script)->not->toBe('');
    expect($updateTask->status)->toBe('running');

    Process::assertRan(fn () => true);
});
