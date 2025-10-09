<?php

use App\Callbacks\UpdateDeploymentStatus;
use App\Jobs\InstallCaddyFileJob;
use App\Models\Deployment;
use App\Models\Site;
use App\Models\Task;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Process;

it('dispatches a job to install the Caddy file if it has not been installed before', function () {
    Process::fake();
    Queue::fake();

    $site = Site::factory()->caddyNotInstalled()->create();
    $deployment = Deployment::factory()->for($site)->create();
    $task = Task::factory()->create();
    $callback = new UpdateDeploymentStatus($deployment->id);

    $callback($task);

    Queue::assertPushed(InstallCaddyFileJob::class, function ($job) use ($site) {
        return $job->site->is($site);
    });
});

it('creates and runs a get_git_hash task for the deployment and updates the deployment commit', function () {
    Queue::fake();
    Process::fake([
        '*' => Process::sequence()
            ->push(Process::result()) // Prepare remote directory
            ->push(Process::result()) // Upload script
            ->push(Process::result(output: 'abcdef1234567890')) // Execute script
    ]);

    $deployment = Deployment::factory()->pending()->create(['commit' => null]);
    $task = Task::factory()->create();

    $callback = new UpdateDeploymentStatus($deployment->id);
    $callback($task);

    $gitHashTask = $deployment->site->server->tasks()
        ->where('name', 'get_git_hash')
        ->first();

    expect($gitHashTask)->not()->toBeNull();

    Process::assertRan(function ($process, $result) {
        return true;
    });

    $deployment->refresh();
    expect($deployment->commit)->toBe('abcdef1234567890');
});

it('sets the deployment status to deployed after running the callback', function () {
    Process::fake();
    Queue::fake();

    $deployment = Deployment::factory()->pending()->create();
    $task = Task::factory()->create();
    $callback = new UpdateDeploymentStatus($deployment->id);

    $callback($task);

    $deployment->refresh();
    expect($deployment->status)->toBe('deployed');
});
