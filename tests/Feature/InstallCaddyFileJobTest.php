<?php

use App\Jobs\InstallCaddyFileJob;
use App\Models\Site;
use Illuminate\Support\Facades\Process;

it('creates a server task to install the Caddy file for the site', function () {
    Process::fake();

    $site = Site::factory()->create();
    $server = $site->server;

    (new InstallCaddyFileJob($site))->handle();

    expect($server->tasks)->toHaveCount(1);

    $task = $server->tasks->first();
    expect($task->name)->toBe('install_caddy_file');
    expect($task->user)->toBe('fuse');
    expect($task->script)->not->toBeNull();
    expect($task->script)->not->toBe('');

    expect($task->status)->toBe('running');
    Process::assertRan(fn () => true);
});
