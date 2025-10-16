<?php

use App\Jobs\InstallDatabaseJob;
use App\Models\Database;

it('can be constructed and handled', function () {
    $database = Database::factory()->create();
    $job = new InstallDatabaseJob($database);
    expect($job->database->is($database))->toBeTrue();
    $job->handle(); // Should not throw
});
