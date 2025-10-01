<?php

use App\Models\Organization;
use App\Models\User;
use Livewire\Volt\Volt;
use App\Services\OrganizationSshKeyService;
use Mockery\MockInterface;

it('renders the registration screen', function () {
    $response = $this->get('/register');

    $response->assertStatus(200);
});

beforeEach(function () {
    $mock = Mockery::mock(\App\Services\OrganizationSshKeyService::class);
    $mock->shouldReceive('createSshKeyPair')
        ->andReturn([
            'privateKey' => 'FAKE_PRIVATE_KEY',
            'publicKey' => 'FAKE_PUBLIC_KEY',
        ]);
    app()->instance(\App\Services\OrganizationSshKeyService::class, $mock);
});

it('registers a new user with valid data', function () {
    $response = Volt::test('auth.register')
        ->set('name', 'Test User')
        ->set('email', 'test@example.com')
        ->set('password', 'password')
        ->set('password_confirmation', 'password')
        ->call('register');

    $response
        ->assertHasNoErrors()
        ->assertRedirect(route('dashboard', absolute: false));

    $this->assertAuthenticated();
});

it('creates a personal organization for the new user on registration', function () {
    $userName = 'Test User';
    $userEmail = 'test2@example.com';

    $response = Volt::test('auth.register')
        ->set('name', $userName)
        ->set('email', $userEmail)
        ->set('password', 'password')
        ->set('password_confirmation', 'password')
        ->call('register');

    $response->assertHasNoErrors();

    $user = User::first();

    expect($user)->not->toBeNull();

    $organization = Organization::first();

    expect($organization)->not->toBeNull();
    expect($organization->user->is($user))->toBeTrue();
    expect($organization->personal)->toBeTrue();
    expect($user->currentOrganization->is($organization))->toBeTrue();
});
