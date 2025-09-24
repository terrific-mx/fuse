<?php

use App\Models\Organization;
use App\Models\User;
use Facades\App\Services\SshKeyGenerator;
use Livewire\Volt\Volt;

it('renders the registration screen', function () {
    $response = $this->get('/register');

    $response->assertStatus(200);
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
    SshKeyGenerator::shouldReceive('generate')
        ->once()
        ->andReturn([
            'public' => 'FAKE_PUBLIC_KEY',
            'private' => 'FAKE_PRIVATE_KEY',
        ]);

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

    expect($organization->ssh_public_key)->toBe('FAKE_PUBLIC_KEY');
    expect($organization->ssh_private_key)->toBe('FAKE_PRIVATE_KEY');
});
