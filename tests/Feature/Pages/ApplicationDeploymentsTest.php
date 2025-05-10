<?php

use App\Jobs\DeployApplication;
use App\Models\Application;
use App\Models\Task;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Facades\Queue;
use Livewire\Volt\Volt;

uses(RefreshDatabase::class);

it('can create a new deployment', function () {
    Process::fake();
    $application = Application::factory()->create();

    Volt::test('pages.applications.deployments', ['application' => $application])
        ->call('deploy');

    expect($application->deployments)->toHaveCount(1);
});

it('runs the deployment script', function () {
    Process::fake();
    $application = Application::factory()->create();

    Volt::test('pages.applications.deployments', ['application' => $application])
        ->call('deploy');

    expect($application->server->tasks()->first())
        ->name->toBe('Deploying application')
        ->user->toBe('fuse');
});

it('marks the deployment as finished on succesful task deployment', function () {
    Process::fake();
    $application = Application::factory()->create();

    Volt::test('pages.applications.deployments', ['application' => $application])
        ->call('deploy');

    tap($application->server->tasks->last(), function (Task $task) {
        expect($task)->name->toBe('Deploying application');

        $task->finish(exitCode: 0);
    });

    expect($application->deployments()->first())
        ->status->toBe('finished');
});

it('marks the deployment as failed on failed task deployment', function () {
    Process::fake();
    $application = Application::factory()->create();

    Volt::test('pages.applications.deployments', ['application' => $application])
        ->call('deploy');

    tap($application->server->tasks->last(), function (Task $task) {
        expect($task)->name->toBe('Deploying application');

        $task->finish(exitCode: 999);
    });

    expect($application->deployments()->first())
        ->status->toBe('failed');
});

it('cannot create a new deployment if the application has a pending deployment', function () {
    $application = Application::factory()->create();
    $application->deployments()->create(['status' => 'pending']);

    Volt::test('pages.applications.deployments', ['application' => $application])
        ->call('deploy');

    expect($application->deployments()->pending()->count())->toBe(1);
});

it('dispatches a deployment event', function () {
    Queue::fake();
    $application = Application::factory()->create();

    Volt::test('pages.applications.deployments', ['application' => $application])
        ->call('deploy');

    Queue::assertPushed(DeployApplication::class);
});
