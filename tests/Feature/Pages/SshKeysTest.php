<?php

use App\Models\SshKey;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Volt\Volt;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

uses(RefreshDatabase::class);

it('stores a valid ssh key for authenticated user', function () {
    $user = User::factory()->create();

    Volt::actingAs($user)->test('pages.ssh-keys.create')
        ->set('name', 'Test SSH Key')
        ->set('public_key', 'ssh-rsa test-public-key')
        ->call('save');

    expect($user->sshKeys)->toHaveCount(1);
    expect($user->sshKeys->first())
        ->name->toBe('Test SSH Key')
        ->public_key->toBe('ssh-rsa test-public-key')
        ->fingerprint->toBeString();
});

it('redirects to ssh-keys after successful creation', function () {
    $user = User::factory()->create();

    Volt::actingAs($user)->test('pages.ssh-keys.create')
        ->set('name', 'Test Key')
        ->set('public_key', 'ssh-rsa test-public-key')
        ->call('save')
        ->assertRedirect('/ssh-keys');
});

it('requires name field', function () {
    $user = User::factory()->create();

    Volt::actingAs($user)->test('pages.ssh-keys.create')
        ->set('name', '')
        ->call('save')
        ->assertHasErrors(['name' => 'required']);
});

it('requires public_key field', function () {
    $user = User::factory()->create();

    Volt::actingAs($user)->test('pages.ssh-keys.create')
        ->set('public_key', '')
        ->call('save')
        ->assertHasErrors(['public_key' => 'required']);
});

it('validates public_key format', function () {
    $user = User::factory()->create();

    Volt::actingAs($user)->test('pages.ssh-keys.create')
        ->set('public_key', 'invalid-key-format')
        ->call('save')
        ->assertHasErrors(['public_key' => 'The public key must be a valid SSH key (starting with ssh-rsa, ssh-ed25519, or ecdsa-sha2-nistp).']);
});

it('limits name to 255 characters', function () {
    $user = User::factory()->create();

    Volt::actingAs($user)->test('pages.ssh-keys.create')
        ->set('name', str_repeat('a', 256))
        ->call('save')
        ->assertHasErrors(['name' => 'max']);
});

it('limits public_key to 4096 characters', function () {
    $user = User::factory()->create();

    Volt::actingAs($user)->test('pages.ssh-keys.create')
        ->set('public_key', 'ssh-rsa '.str_repeat('a', 4090))
        ->call('save')
        ->assertHasErrors(['public_key' => 'max']);
});

it('requires unique name per user', function () {
    $user = User::factory()->create();
    $user->sshKeys()->create([
        'name' => 'Duplicate Name',
        'public_key' => 'ssh-rsa ::key1::'
    ]);

    Volt::actingAs($user)->test('pages.ssh-keys.create')
        ->set('name', 'Duplicate Name')
        ->set('public_key', 'ssh-rsa ::key2::')
        ->call('save')
        ->assertHasErrors(['name' => 'unique']);
});

it('allows same name for different users', function () {
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();

    $user1->sshKeys()->create([
        'name' => 'Shared Name',
        'public_key' => 'ssh-rsa ::key1::'
    ]);

    Volt::actingAs($user2)->test('pages.ssh-keys.create')
        ->set('name', 'Shared Name')
        ->set('public_key', 'ssh-rsa ::key2::')
        ->call('save');

    expect($user2->sshKeys)->toHaveCount(1);
});

it('requires authentication', function () {
    get('/ssh-keys')->assertRedirect('/login');
});

it('allows access for authenticated users', function () {
    /** @var User */
    $user = User::factory()->create();

    actingAs($user)->get('/ssh-keys')->assertOk();
});

it('redirects unauthenticated users from create page', function () {
    get('/ssh-keys/create')->assertRedirect('/login');
});

it('allows authenticated users to access create page', function () {
    /** @var User */
    $user = User::factory()->create();

    actingAs($user)->get('/ssh-keys/create')->assertOk();
});

it('only displays ssh keys for authenticated user', function () {
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();

    SshKey::factory()->for($user1)->create(['name' => 'User1 Key']);
    SshKey::factory()->for($user2)->create();

    Volt::actingAs($user1)
        ->test('pages.ssh-keys')
        ->assertSet('sshKeys', function ($keys) {
            expect($keys)->toHaveCount(1);
            expect($keys->first())->name->toBe('User1 Key');

            return true;
        });
});
