<?php

use App\Models\User;
use Livewire\Volt\Volt;

use function Pest\Laravel\actingAs;

beforeEach(function () {
    $mock = Mockery::mock(\App\Services\OrganizationSshKeyService::class);
    $mock->shouldReceive('createSshKeyPair')
        ->andReturn([
            'privateKey' => 'FAKE_PRIVATE_KEY',
            'publicKey' => 'FAKE_PUBLIC_KEY',
        ]);
    app()->instance(\App\Services\OrganizationSshKeyService::class, $mock);
});

it('creates a new organization for the user with valid data', function () {
    $user = User::factory()->create();
    actingAs($user);

    $orgName = 'Acme Inc';
    $response = Volt::test('organizations.create')
        ->set('name', $orgName)
        ->call('create');

    $response->assertHasNoErrors();
    $user->refresh();
    $organization = $user->organizations()->where('name', $orgName)->first();
    expect($organization)->not->toBeNull();
    expect($user->currentOrganization->is($organization))->toBeTrue();
    $response->assertRedirect(route('dashboard'));
});

it('shows validation errors for missing or invalid organization name', function () {
    $user = User::factory()->create();
    actingAs($user);

    $response = Volt::test('organizations.create')
        ->set('name', '')
        ->call('create');

    $response->assertHasErrors(['name']);
});

it('guests cannot create organizations', function () {
    $orgName = 'Gamma Ltd';
    $response = Volt::test('organizations.create')
        ->set('name', $orgName)
        ->call('create');

    $response->assertForbidden();
});
