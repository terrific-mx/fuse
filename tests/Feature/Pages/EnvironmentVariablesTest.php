<?php

use App\Models\Application;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Process;
use Livewire\Volt\Volt;

uses(RefreshDatabase::class);

it('creates a task to fetch the environment file', function () {
    Process::fake();
    $application = Application::factory()->create();
    $user = $application->user();

    Volt::actingAs($user)->test('pages.applications.environment-variables', ['application' => $application])
        ->call('getEnvironmentFile');

    expect($application->server->tasks)->toHaveCount(1);
    expect($application->server->tasks->first()->name)->toBe('Fetching .env File');
});

it('creates a task to save the environment file', function () {
    Process::fake();
    $application = Application::factory()->create();
    $user = $application->user();

    Volt::actingAs($user)->test('pages.applications.environment-variables', ['application' => $application])
        ->set('environmentVariables', 'APP_NAME=Fuse')
        ->call('save');

    expect($application->server->tasks)->toHaveCount(1);
    expect($application->server->tasks->first()->name)->toBe('Saving .env File');
});
