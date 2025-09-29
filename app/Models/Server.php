<?php

namespace App\Models;

use App\Jobs\ProvisionServer;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Server extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected function casts(): array
    {
        return [];
    }

    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    public function provider()
    {
        return $this->belongsTo(ServerProvider::class, 'provider_id');
    }

    /**
     * Provision the server using the provider client.
     */
    public function provision(): void
    {
        $providerId = $this->provider->client()->createServer($this->name, $this->type, $this->region);

        $this->update(['provider_server_id' => $providerId]);

        ProvisionServer::dispatch($this);
    }

    /**
     * Determine if the server is currently provisioning.
     */
    public function isProvisioning(): bool
    {
        return $this->status === 'provisioning';
    }

    /**
     * Determine if the server is provisioned.
     */
    public function isProvisioned(): bool
    {
        return $this->status === 'provisioned';
    }

    /**
     * Determine if the server is older than the given minutes.
     */
    public function isOlderThanMinutes(int $minutes): bool
    {
        return $this->created_at->lt(now()->subMinutes($minutes));
    }

    /**
     * Stub: Check if the server is ready for provisioning.
     */
    public function isReadyForProvisioning(): bool
    {
        if (! $this->ip_address) {
            $this->retrieveIpFromProvider();
        }

        if (! $this->refresh()->ip_address) {
            return false;
        }

        // TODO: Implement actual readiness checks
        return true;
    }

    /**
     * Retrieve the IP address from the server provider.
     */
    public function retrieveIpFromProvider(): void
    {
        if ($ip = $this->provider->client()->getServerIp($this)) {
            $this->update(['ip_address' => $ip]);
        }
    }

    /**
     * Run the provisioning script for this server.
     */
    public function runProvisioningScript(): void
    {
        if ($this->isProvisioning()) {
            return;
        }

        $this->update(['status' => 'provisioning']);

        // TODO: Add actual provisioning script logic here
    }

    public function sites()
    {
        return $this->hasMany(Site::class);
    }

    public function databases()
    {
        return $this->hasMany(Database::class);
    }
}
