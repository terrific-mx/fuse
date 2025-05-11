<?php

use App\Jobs\DeleteApplication;
use App\Models\Application;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Facades\Queue;
use Livewire\Volt\Volt;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

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

it('checks only authorized users can view the delete application page', function () {
    $application = Application::factory()->create();
    $user = $application->user();
    /** @var User */
    $anotherUser = User::factory()->create();

    actingAs($anotherUser)->get("applications/{$application->id}/delete")->assertForbidden();
});

it('checks unauthenticated users are redirected to login page', function () {
    $application = Application::factory()->create();

    get("applications/{$application->id}/delete")->assertRedirect('/login');
});

it('dispatches a job to delete the application from the server', function () {
    Queue::fake();
    $application = Application::factory()->create();
    $user = $application->user();

    Volt::actingAs($user)->test('pages.applications.delete', ['application' => $application])
        ->call('delete');

    Queue::assertPushed(DeleteApplication::class);
});
