<?php

use App\Callbacks\MarkCaddyInstalled;
use App\Models\Site;
use App\Models\Task;

it('marks the site as having its Caddy file installed', function () {
    $site = Site::factory()->create(['caddy_installed_at' => null]);
    $task = Task::factory()->for($site->server)->create([
        'name' => 'install_caddy_file',
    ]);

    $callback = new MarkCaddyInstalled($site->id);
    $callback($task);

    $site->refresh();
    expect($site->caddy_installed_at)->not->toBeNull();
});
