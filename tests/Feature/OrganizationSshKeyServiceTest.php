<?php

use App\Models\Organization;
use App\Services\OrganizationSshKeyService;
use Illuminate\Support\Facades\Process;

it('generates and returns an SSH key pair', function () {
    Process::fake();
    $organization = Organization::factory()->create();
    $service = new OrganizationSshKeyService;

    // Prepare dummy key content
    $dummyPrivate = "-----BEGIN OPENSSH PRIVATE KEY-----\nFAKEKEYDATA\n-----END OPENSSH PRIVATE KEY-----\n";
    $dummyPublic = "ssh-rsa AAAAB3NzaC1yc2EAAAADAQABAAABAQCFAKEKEYDATA org-{$organization->id}";

    // Simulate file creation as if ssh-keygen ran
    $privateKeyPath = storage_path('app/ssh-keys/org_'.$organization->id.'_id_rsa');
    $publicKeyPath = $privateKeyPath.'.pub';
    if (! is_dir(storage_path('app/ssh-keys'))) {
        mkdir(storage_path('app/ssh-keys'), 0700, true);
    }
    file_put_contents($privateKeyPath, $dummyPrivate);
    file_put_contents($publicKeyPath, $dummyPublic);

    Process::shouldReceive('run')->andReturn(
        new class
        {
            public function successful()
            {
                return true;
            }

            public function errorOutput()
            {
                return '';
            }
        }
    );

    $keyPair = $service->createSshKeyPair($organization);

    expect($keyPair['privateKey'])->toBe($dummyPrivate);
    expect($keyPair['publicKey'])->toBe($dummyPublic);
});

it('writes and deletes the private key to storage', function () {
    $dummyKey = "-----BEGIN OPENSSH PRIVATE KEY-----\nb3BlbnNzaC1rZXktdjEAAAAABG5vbmUAAAAEbm9uZQAAAAAAAAABAAABFwAAAAdzc2gtcn\nNhAAAAAwEAAQAAAYEAz0Qn3Qw6Qw==\n-----END OPENSSH PRIVATE KEY-----\n";
    $organization = Organization::factory()->create(['ssh_private_key' => $dummyKey]);
    $service = new OrganizationSshKeyService;

    $keyPath = $service->writePrivateKeyToStorage($organization);

    expect(file_exists($keyPath))->toBeTrue();
    expect(fileperms($keyPath) & 0777)->toBe(0600);

    $service->deletePrivateKeyFromStorage($organization);

    expect(file_exists($keyPath))->toBeFalse();
});
