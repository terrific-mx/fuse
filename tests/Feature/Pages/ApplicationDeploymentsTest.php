<?php

use App\Models\Application;
use App\Models\Task;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Process;
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
        ->name->toBe('Deploying application without downtime')
        ->user->toBe('fuse');
});

it('marks the deployment as finished on succesful task deployment', function () {
    Process::fake();
    $application = Application::factory()->create();

    Volt::test('pages.applications.deployments', ['application' => $application])
        ->call('deploy');

    tap($application->server->tasks->last(), function (Task $task) {
        expect($task)->name->toBe('Deploying application without downtime');

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
        expect($task)->name->toBe('Deploying application without downtime');

        $task->finish(exitCode: 999);
    });

    expect($application->deployments()->first())
        ->status->toBe('failed');
});
