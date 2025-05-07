<?php

use App\Models\Application;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Volt\Volt;

uses(RefreshDatabase::class);

it('can update the settings', function () {
    $application = Application::factory()->create([
        'repository' => 'example/another-valid-repository',
        'branch' => 'another-valid-branch',
        'web_directory' => 'public_html',
        'php_version' => 'PHP 8.2'
    ]);
    $user = $application->user();

    $component = Volt::actingAs($user)->test('pages.applications.settings', ['application' => $application])
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
