<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Organization;
use App\Services\OrganizationSshKeyService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrganizationSshKeyServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_generate_and_store_ssh_key_pair(): void
    {
        $org = Organization::factory()->create();
        $service = new OrganizationSshKeyService();
        $service->generateAndStoreSshKeyPair($org);
        $org->refresh();
        $this->assertNotEmpty($org->ssh_private_key);
        $this->assertNotEmpty($org->ssh_public_key);
        $this->assertStringContainsString('PRIVATE KEY', $org->ssh_private_key);
        $this->assertStringContainsString('ssh-rsa', $org->ssh_public_key);
    }

    public function test_write_and_delete_private_key_to_storage(): void
    {
        $dummyKey = "-----BEGIN OPENSSH PRIVATE KEY-----\nb3BlbnNzaC1rZXktdjEAAAAABG5vbmUAAAAEbm9uZQAAAAAAAAABAAABFwAAAAdzc2gtcn\nNhAAAAAwEAAQAAAYEAz0Qn3Qw6Qw==\n-----END OPENSSH PRIVATE KEY-----\n";
        $org = Organization::factory()->create([
            'ssh_private_key' => $dummyKey,
        ]);
        $service = new OrganizationSshKeyService();
        $keyPath = $service->writePrivateKeyToStorage($org);
        $this->assertFileExists($keyPath);
        $this->assertEquals(0600, fileperms($keyPath) & 0777);
        $service->deletePrivateKeyFromStorage($org);
        $this->assertFileDoesNotExist($keyPath);
    }
}
