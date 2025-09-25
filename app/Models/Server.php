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
     * Provision the server using the provider client.
     */
    public function provision(): void
    {
        $providerId = $this->provider->client()->createServer($this->name, $this->type, $this->region);

        $this->update([
            'provider_server_id' => $providerId,
        ]);
    }
}
