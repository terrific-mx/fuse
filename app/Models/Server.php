<?php

namespace App\Models;

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
     * Get the provider client for this server.
     */
    public function providerClient()
    {
        return $this->provider->client();
    }

    /**
     * Provision the server using the provider client.
     */
    public function provision(): void
    {
        $providerId = $this->providerClient()->createServer($this->name, $this->type, $this->region);
        // Simulate saving the provider server id
        $this->provider_server_id = $providerId;
        // In real code, you would $this->save();
    }
}
