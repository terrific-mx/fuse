<?php

use App\Models\Organization;
use App\Services\OrganizationSshKeyService;

it('generates and stores an SSH key pair', function () {
    $org = Organization::factory()->create();
    $service = new OrganizationSshKeyService();
    $service->generateAndStoreSshKeyPair($org);
    $org->refresh();
    expect($org->ssh_private_key)->not->toBeEmpty();
    expect($org->ssh_public_key)->not->toBeEmpty();
    expect($org->ssh_private_key)->toContain('PRIVATE KEY');
    expect($org->ssh_public_key)->toContain('ssh-rsa');
});

it('writes and deletes the private key to storage', function () {
    $dummyKey = "-----BEGIN OPENSSH PRIVATE KEY-----\nb3BlbnNzaC1rZXktdjEAAAAABG5vbmUAAAAEbm9uZQAAAAAAAAABAAABFwAAAAdzc2gtcn\nNhAAAAAwEAAQAAAYEAz0Qn3Qw6Qw==\n-----END OPENSSH PRIVATE KEY-----\n";
    $org = Organization::factory()->create([
        'ssh_private_key' => $dummyKey,
    ]);
    $service = new OrganizationSshKeyService();
    $keyPath = $service->writePrivateKeyToStorage($org);
    expect(file_exists($keyPath))->toBeTrue();
    expect(fileperms($keyPath) & 0777)->toBe(0600);
    $service->deletePrivateKeyFromStorage($org);
    expect(file_exists($keyPath))->toBeFalse();
});
