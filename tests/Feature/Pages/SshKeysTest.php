<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Volt\Volt;

uses(RefreshDatabase::class);

it('can store the users ssh key', function () {
    $user = User::factory()->create();

    Volt::actingAs($user)->test('pages.ssh-keys')
        ->set('name', 'Test SSH Key')
        ->set('public_key', 'ssh-rsa ::public-key::')
        ->call('save');

    expect($user->sshKeys)->toHaveCount(1);
    expect($user->sshKeys->first())
        ->name->toBe('Test SSH Key')
        ->public_key->toBe('ssh-rsa ::public-key::');
});

it('requires name field', function () {
    $user = User::factory()->create();

    Volt::actingAs($user)->test('pages.ssh-keys')
        ->set('name', '')
        ->call('save')
        ->assertHasErrors(['name' => 'required']);
});

it('requires public_key field', function () {
    $user = User::factory()->create();

    Volt::actingAs($user)->test('pages.ssh-keys')
        ->set('public_key', '')
        ->call('save')
        ->assertHasErrors(['public_key' => 'required']);
});

it('validates public_key format', function () {
    $user = User::factory()->create();

    Volt::actingAs($user)->test('pages.ssh-keys')
        ->set('public_key', 'invalid-key-format')
        ->call('save')
        ->assertHasErrors(['public_key' => 'The public key must be a valid SSH key (starting with ssh-rsa, ssh-ed25519, or ecdsa-sha2-nistp).']);
});

it('limits name to 255 characters', function () {
    $user = User::factory()->create();

    Volt::actingAs($user)->test('pages.ssh-keys')
        ->set('name', str_repeat('a', 256))
        ->call('save')
        ->assertHasErrors(['name' => 'max']);
});

it('limits public_key to 4096 characters', function () {
    $user = User::factory()->create();

    Volt::actingAs($user)->test('pages.ssh-keys')
        ->set('public_key', 'ssh-rsa '.str_repeat('a', 4090))
        ->call('save')
        ->assertHasErrors(['public_key' => 'max']);
});
