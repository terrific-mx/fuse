<?php

use App\Models\SourceProvider;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Volt\Volt;

uses(RefreshDatabase::class);

it('successfully creates a new source control provider with valid data', function () {
    $user = User::factory()->create();

    Volt::actingAs($user)->test('pages.settings.source-control.create')
        ->set('name', 'Test Provider')
        ->set('type', 'FakeSourceProvider')
        ->set('token', 'valid-token')
        ->call('add');

    expect($user->sourceProviders)->toHaveCount(1);

    expect($user->sourceProviders()->first())
        ->name->toBe('Test Provider')
        ->type->toBe('FakeSourceProvider')
        ->token->toBe('valid-token');
});

it('validates the name field is required', function () {
    $user = User::factory()->create();

    Volt::actingAs($user)->test('pages.settings.source-control.create')
        ->set('name', '')
        ->call('add')
        ->assertHasErrors(['name' => 'required']);
});

it('validates the name field is unique for the user', function () {
    $user = User::factory()->create();

    SourceProvider::factory()->for($user)->create([
        'name' => 'Test Provider',
    ]);

    Volt::actingAs($user)->test('pages.settings.source-control.create')
        ->set('name', 'Test Provider')
        ->call('add')
        ->assertHasErrors(['name' => 'unique']);
});

it('validates the name field does not exceed 255 characters', function () {
    $user = User::factory()->create();

    Volt::actingAs($user)->test('pages.settings.source-control.create')
        ->set('name', str_repeat('a', 256))
        ->call('add')
        ->assertHasErrors(['name' => 'max']);
});

it('validates the type field is required', function () {
    $user = User::factory()->create();

    Volt::actingAs($user)->test('pages.settings.source-control.create')
        ->set('type', '')
        ->call('add')
        ->assertHasErrors(['type' => 'required']);
});

it('validates the type field only accepts GitHub or FakeSourceProvider', function () {
    $user = User::factory()->create();

    Volt::actingAs($user)->test('pages.settings.source-control.create')
        ->set('type', 'InvalidProvider')
        ->call('add')
        ->assertHasErrors(['type' => 'in']);
});

it('validates the token field is required', function () {
    $user = User::factory()->create();

    Volt::actingAs($user)->test('pages.settings.source-control.create')
        ->set('token', '')
        ->call('add')
        ->assertHasErrors(['token' => 'required']);
});

it('validates the token is valid', function () {
    $user = User::factory()->create();

    Volt::actingAs($user)->test('pages.settings.source-control.create')
        ->set('name', 'Test Provider')
        ->set('type', 'FakeSourceProvider')
        ->set('token', 'invalid-token')
        ->call('add')
        ->assertHasErrors(['token' => 'The given credentials are invalid.']);

    expect($user->sourceProviders)->toHaveCount(0);
});
