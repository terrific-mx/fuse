<?php

use App\Models\Application;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Volt\Volt;

uses(RefreshDatabase::class);

it('can update the deployment settings', function () {
    $application = Application::factory()->create();
    $user = $application->user();

    Volt::actingAs($user)->test('pages.applications.deployment-settings', ['application' => $application])
        ->set('releases_to_retain', 10)
        ->set('shared_directories', '/path/to/shared/directory')
        ->set('writable_directories', '/path/to/writable/directory')
        ->set('shared_files', '/path/to/shared/file')
        ->set('before_update_hook', 'echo "before update hook"')
        ->set('after_update_hook', 'echo "after update hook"')
        ->set('before_activate_hook', 'echo "before activate hook"')
        ->set('after_activate_hook', 'echo "after activate hook"')
        ->call('save');

    expect($application->fresh())
        ->releases_to_retain->toBe(10)
        ->shared_directories->toBe(['/path/to/shared/directory'])
        ->writable_directories->toBe(['/path/to/writable/directory'])
        ->shared_files->toBe(['/path/to/shared/file'])
        ->before_update_hook->toBe('echo "before update hook"')
        ->after_update_hook->toBe('echo "after update hook"')
        ->before_activate_hook->toBe('echo "before activate hook"')
        ->after_activate_hook->toBe('echo "after activate hook"');
});

it('validates releases_to_retain is required', function () {
    $application = Application::factory()->create();
    $user = $application->user();

    Volt::actingAs($user)->test('pages.applications.deployment-settings', ['application' => $application])
        ->set('releases_to_retain', '')
        ->call('save')
        ->assertHasErrors(['releases_to_retain' => 'required']);
});

it('validates releases_to_retain is an integer', function () {
    $application = Application::factory()->create();
    $user = $application->user();

    Volt::actingAs($user)->test('pages.applications.deployment-settings', ['application' => $application])
        ->set('releases_to_retain', 'not an integer')
        ->call('save')
        ->assertHasErrors(['releases_to_retain' => 'integer']);
});

it('validates releases_to_retain is at least 1', function () {
    $application = Application::factory()->create();
    $user = $application->user();

    Volt::actingAs($user)->test('pages.applications.deployment-settings', ['application' => $application])
        ->set('releases_to_retain', 0)
        ->call('save')
        ->assertHasErrors(['releases_to_retain' => 'min']);
});

it('validates releases_to_retain is at most 50', function () {
    $application = Application::factory()->create();
    $user = $application->user();

    Volt::actingAs($user)->test('pages.applications.deployment-settings', ['application' => $application])
        ->set('releases_to_retain', 51)
        ->call('save')
        ->assertHasErrors(['releases_to_retain' => 'max']);
});
