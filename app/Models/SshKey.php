<?php

namespace App\Models;

use App\Jobs\AuthorizeSshKeyOnServerJob;
use App\Jobs\DeauthorizeSshKeyOnServerJob;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class SshKey extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    public function servers()
    {
        return $this->belongsToMany(Server::class, 'server_ssh_key');
    }

    /**
     * Get the masked public key (first 10 and last 10 chars visible).
     */
    protected function maskedPublicKey(): Attribute
    {
        return new Attribute(
            get: fn ($value, $attributes) => (
                strlen($attributes['public_key']) <= 20
                    ? $attributes['public_key']
                    : Str::mask($attributes['public_key'], '*', 10, max(0, strlen($attributes['public_key']) - 20))
            ),
        );
    }

    /**
     * Authorize this SSH key on the given server.
     */
    public function authorize(Server $server): void
    {
        $server->createAuthorizeSshKeyTask($this)->run();
    }

    /**
     * Sync servers and dispatch jobs for authorization/deauthorization.
     */
    public function syncServers(Collection $servers): void
    {
        $current = $this->servers()->get();
        $selected = $servers;

        $currentIds = $current->pluck('id');
        $selectedIds = $selected->pluck('id');

        $toAuthorize = $selected->whereNotIn('id', $currentIds);
        $toDeauthorize = $current->whereNotIn('id', $selectedIds);

        $toAuthorize->each(fn ($server) => dispatch(new AuthorizeSshKeyOnServerJob($this, $server)));
        $toDeauthorize->each(fn ($server) => dispatch(new DeauthorizeSshKeyOnServerJob($this, $server)));

        $this->servers()->sync($selectedIds->toArray());
    }
}
