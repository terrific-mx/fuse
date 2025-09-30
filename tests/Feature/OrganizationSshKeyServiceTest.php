<?php

use App\Models\Organization;
use App\Services\OrganizationSshKeyService;

use Illuminate\Support\Facades\Process;

it('generates and stores an SSH key pair', function () {
    $org = Organization::factory()->create();
    $service = new OrganizationSshKeyService();

    // Fake the Process facade
    Process::fake();
    Process::shouldReceive('run')->andReturn(
        new class {
            public function successful() { return true; }
            public function errorOutput() { return ''; }
        }
    );

    // Prepare dummy key files
    $privateKeyPath = storage_path('app/ssh-keys/org_' . $org->id . '_id_rsa');
    $publicKeyPath = $privateKeyPath . '.pub';
    $dummyPrivate = '-----BEGIN PRIVATE KEY-----\nFAKEKEYDATA\n-----END PRIVATE KEY-----';
    $dummyPublic = 'ssh-rsa AAAAB3NzaC1yc2EAAAADAQABAAABAQCFAKEKEYDATA org-'. $org->id;

    if (!is_dir(dirname($privateKeyPath))) {
        mkdir(dirname($privateKeyPath), 0700, true);
    }
    file_put_contents($privateKeyPath, $dummyPrivate);
    file_put_contents($publicKeyPath, $dummyPublic);

    $service->generateAndStoreSshKeyPair($org);
    $org->refresh();
    expect($org->ssh_private_key)->toBe($dummyPrivate);
    expect($org->ssh_public_key)->toBe($dummyPublic);
    expect($org->ssh_private_key)->toContain('PRIVATE KEY');
    expect($org->ssh_public_key)->toContain('ssh-rsa');

    // Clean up
    if (file_exists($privateKeyPath)) unlink($privateKeyPath);
    if (file_exists($publicKeyPath)) unlink($publicKeyPath);
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
