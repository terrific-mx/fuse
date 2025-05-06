<?php

use App\Jobs\DeleteApplication;
use App\Models\Application;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Facades\Queue;
use Livewire\Volt\Volt;

uses(RefreshDatabase::class);

it('can delete an application', function () {
    Process::fake();
    $application = Application::factory()->create();
    $user = $application->user();

    Volt::actingAs($user)->test('pages.applications.delete', ['application' => $application])
        ->call('delete');

    expect(Application::count())->toBe(0);
});

it('runs script to upade all caddy imports', function () {
    Process::fake();
    $application = Application::factory()->create();
    $user = $application->user();

    Volt::actingAs($user)->test('pages.applications.delete', ['application' => $application])
        ->call('delete');

    expect($application->server->tasks->first())->name->toBe('Updating Caddy Imports');
});

it('run script to delete application folder', function () {
    Process::fake();
    $application = Application::factory()->create();
    $user = $application->user();

    Volt::actingAs($user)->test('pages.applications.delete', ['application' => $application])
        ->call('delete');

    expect($application->server->tasks)->toHaveCount(2);
    expect($application->server->tasks->last())->name->toBe('Deleting folder');
});

it('checks only authorized users can delete applications', function () {
    Process::fake();
    $application = Application::factory()->create();
    $user = $application->user();
    $anotherUser = User::factory()->create();

    Volt::actingAs($anotherUser)->test('pages.applications.delete', ['application' => $application])
        ->call('delete')
        ->assertForbidden();
});

it('dispatches a job to delete the application from the server', function () {
    Queue::fake();
    $application = Application::factory()->create();
    $user = $application->user();

    Volt::actingAs($user)->test('pages.applications.delete', ['application' => $application])
        ->call('delete');

    Queue::assertPushed(DeleteApplication::class);
});
