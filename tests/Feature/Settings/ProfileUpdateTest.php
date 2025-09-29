<?php

use App\Models\User;
use Livewire\Volt\Volt;

it('displays the profile page', function () {
    $user = User::factory()->withPersonalOrganization()->create();
    $this->actingAs($user);

    $this->get('/settings/profile')->assertOk();
});

it('updates the profile information', function () {
    $user = User::factory()->withPersonalOrganization()->create();

    $this->actingAs($user);

    $response = Volt::test('settings.profile')
        ->set('name', 'Test User')
        ->set('email', 'test@example.com')
        ->call('updateProfileInformation');

    $response->assertHasNoErrors();

    $user->refresh();

    expect($user->name)->toEqual('Test User');
    expect($user->email)->toEqual('test@example.com');
    expect($user->email_verified_at)->toBeNull();
});

it('does not change email verification status when email is unchanged', function () {
    $user = User::factory()->withPersonalOrganization()->create();

    $this->actingAs($user);

    $response = Volt::test('settings.profile')
        ->set('name', 'Test User')
        ->set('email', $user->email)
        ->call('updateProfileInformation');

    $response->assertHasNoErrors();

    expect($user->refresh()->email_verified_at)->not->toBeNull();
});

it('deletes the user account with the correct password', function () {
    $user = User::factory()->withPersonalOrganization()->create();

    $this->actingAs($user);

    $response = Volt::test('settings.delete-user-form')
        ->set('password', 'password')
        ->call('deleteUser');

    $response
        ->assertHasNoErrors()
        ->assertRedirect('/');

    expect($user->fresh())->toBeNull();
    expect(auth()->check())->toBeFalse();
});

it('requires correct password to delete account', function () {
    $user = User::factory()->withPersonalOrganization()->create();

    $this->actingAs($user);

    $response = Volt::test('settings.delete-user-form')
        ->set('password', 'wrong-password')
        ->call('deleteUser');

    $response->assertHasErrors(['password']);

    expect($user->fresh())->not->toBeNull();
});
