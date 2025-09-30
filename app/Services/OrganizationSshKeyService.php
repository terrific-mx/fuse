<?php

namespace App\Services;

use Illuminate\Support\Facades\Process;
use Illuminate\Support\Facades\Storage;
use App\Models\Organization;
use Illuminate\Support\Facades\Log;

class OrganizationSshKeyService
{
    /**
     * Generate an SSH key pair for the given organization and store in DB.
     */
    public function generateAndStoreSshKeyPair(Organization $organization): void
    {
        $tmpDir = storage_path('app/ssh-keys');
        if (!is_dir($tmpDir)) {
            mkdir($tmpDir, 0700, true);
        }
        $privateKeyPath = $tmpDir . "/org_{$organization->id}_id_rsa";
        $publicKeyPath = $privateKeyPath . ".pub";

        // Generate key pair
        $process = Process::run(
            "ssh-keygen -t rsa -b 4096 -N '' -f {$privateKeyPath} -C 'org-{$organization->id}'"
        );
        if (!$process->successful()) {
            throw new \RuntimeException('Failed to generate SSH key pair: ' . $process->errorOutput());
        }

        // Read keys
        $privateKey = file_get_contents($privateKeyPath);
        $publicKey = file_get_contents($publicKeyPath);

        // Store in DB
        $organization->ssh_private_key = $privateKey;
        $organization->ssh_public_key = $publicKey;
        $organization->save();

        // Cleanup
        unlink($privateKeyPath);
        unlink($publicKeyPath);
    }

    /**
     * Write the private key to storage/app/ssh-keys/org_{org_id}_id_rsa for SSH operations.
     */
    public function writePrivateKeyToStorage(Organization $organization): string
    {
        $dir = storage_path('app/ssh-keys');
        if (!is_dir($dir)) {
            mkdir($dir, 0700, true);
        }
        $keyPath = $dir . "/org_{$organization->id}_id_rsa";
        file_put_contents($keyPath, $organization->ssh_private_key);
        chmod($keyPath, 0600);
        return $keyPath;
    }

    /**
     * Delete the private key file from storage after use.
     */
    public function deletePrivateKeyFromStorage(Organization $organization): void
    {
        $keyPath = storage_path('app/ssh-keys/org_' . $organization->id . '_id_rsa');
        if (file_exists($keyPath)) {
            unlink($keyPath);
        }
    }
}
