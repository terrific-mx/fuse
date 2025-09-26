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
     * Stub: Check if the server is ready for provisioning.
     */
    public function isReadyForProvisioning(): bool
    {
        // TODO: Implement actual readiness checks
        return true;
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
}
