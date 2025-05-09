<?php

use App\Jobs\UpdateApplicationCaddyFile;
use App\Models\Application;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Facades\Queue;
use Livewire\Volt\Volt;

uses(RefreshDatabase::class);

it('can update the application settings', function () {
    Process::fake();
    $application = Application::factory()->create([
        'repository' => 'example/another-valid-repository',
        'branch' => 'another-valid-branch',
        'web_directory' => 'public_html',
        'php_version' => 'PHP 8.2',
    ]);
    $user = $application->user();

    Volt::actingAs($user)->test('pages.applications.settings', ['application' => $application])
        ->set('repository', 'example/valid-repository')
        ->set('branch', 'valid-branch')
        ->set('web_directory', 'public')
        ->set('php_version', 'PHP 8.3')
        ->call('save');

    expect($application->fresh())
        ->repository->toBe('example/valid-repository')
        ->branch->toBe('valid-branch')
        ->web_directory->toBe('public')
        ->php_version->toBe('PHP 8.3');
});

it('runs the script to update caddyfile with new settings', function () {
    Process::fake();
    $application = Application::factory()->create();
    $user = $application->user();

    Volt::actingAs($user)->test('pages.applications.settings', ['application' => $application])
        ->call('save');

    expect($application->server->tasks->last())->name->toBe('Updating Caddyfile');
});

it('dispatches a job to update the application caddyfile when the application settings are saved', function () {
    Queue::fake();

    $application = Application::factory()->create();
    $user = $application->user();

    Volt::actingAs($user)->test('pages.applications.settings', ['application' => $application])
        ->call('save');

    Queue::assertPushed(UpdateApplicationCaddyFile::class);
});

it('creates a new application deployument', function () {})->todo();

it('runs the script to deploy the application', function () {})->todo();
