<?php

use App\Models\Organization;
use App\Models\User;
use Illuminate\Support\Facades\Process;
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
    Process::fake([
        'ssh-keygen*' => Process::result(
            output: '',
            errorOutput: '',
            exitCode: 0,
        ),
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

    // Assert SSH keys are set
    expect($organization->ssh_public_key)->not->toBeEmpty();
    expect($organization->ssh_private_key)->not->toBeEmpty();

    // Assert process was run
    Process::assertRan(function ($process) {
        return str_starts_with($process->command, 'ssh-keygen -t rsa -b 4096 -f ' . storage_path('app/private/laravel_orgkey_')) && str_ends_with($process->command, " -N ''");
    });
});
