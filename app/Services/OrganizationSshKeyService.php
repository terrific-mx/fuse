<?php

namespace App\Services;

use App\Models\Organization;
use Illuminate\Support\Facades\Process;

class OrganizationSshKeyService
{
    /**
     * Generate an SSH key pair for the given organization and store in the database.
     *
     * @throws \RuntimeException
     */
    public function generateAndStoreSshKeyPair(Organization $organization): void
    {
        $this->ensureSshKeyDirectoryExists();
        $privateKeyPath = $this->privateKeyPath($organization);
        $publicKeyPath = $this->publicKeyPath($organization);

        $this->generateSshKeyPair($organization, $privateKeyPath);

        $privateKey = file_get_contents($privateKeyPath);
        $publicKey = file_get_contents($publicKeyPath);

        $organization->ssh_private_key = $privateKey;
        $organization->ssh_public_key = $publicKey;
        $organization->save();

        $this->cleanupKeyFiles([$privateKeyPath, $publicKeyPath]);
    }

    /**
     * Write the organization's private key to storage for SSH operations.
     *
     * @return string Path to the written private key file
     */
    public function writePrivateKeyToStorage(Organization $organization): string
    {
        $this->ensureSshKeyDirectoryExists();
        $privateKeyPath = $this->privateKeyPath($organization);
        file_put_contents($privateKeyPath, $organization->ssh_private_key);
        chmod($privateKeyPath, 0600);

        return $privateKeyPath;
    }

    /**
     * Delete the private key file from storage after use.
     */
    public function deletePrivateKeyFromStorage(Organization $organization): void
    {
        $privateKeyPath = $this->privateKeyPath($organization);

        if (file_exists($privateKeyPath)) {
            unlink($privateKeyPath);
        }
    }

    /**
     * Ensure the SSH key directory exists.
     */
    private function ensureSshKeyDirectoryExists(): void
    {
        $dir = storage_path('app/ssh-keys');

        if (! is_dir($dir)) {
            mkdir($dir, 0700, true);
        }
    }

    /**
     * Get the private key file path for the organization.
     */
    private function privateKeyPath(Organization $organization): string
    {
        return storage_path('app/ssh-keys/org_'.$organization->id.'_id_rsa');
    }

    /**
     * Get the public key file path for the organization.
     */
    private function publicKeyPath(Organization $organization): string
    {
        return $this->privateKeyPath($organization).'.pub';
    }

    /**
     * Generate an SSH key pair using ssh-keygen.
     *
     * @throws \RuntimeException
     */
    private function generateSshKeyPair(Organization $organization, string $privateKeyPath): void
    {
        $process = Process::run(
            "ssh-keygen -t rsa -b 4096 -N '' -f {$privateKeyPath} -C 'org-{$organization->id}'"
        );

        if (! $process->successful()) {
            throw new \RuntimeException('Failed to generate SSH key pair: '.$process->errorOutput());
        }
    }

    /**
     * Remove key files from disk.
     */
    private function cleanupKeyFiles(array $paths): void
    {
        foreach ($paths as $path) {
            if (file_exists($path)) {
                unlink($path);
            }
        }
    }
}
