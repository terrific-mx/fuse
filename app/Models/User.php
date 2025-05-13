<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\SecureShellKey;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'workos_id',
        'avatar',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'workos_id',
        'remember_token',
    ];

    public function servers()
    {
        return $this->hasMany(Server::class);
    }

    public function provisionedServers()
    {
        return $this->servers()->where('status', 'provisioned');
    }

    public function applications()
    {
        return $this->hasManyThrough(Application::class, Server::class);
    }

    public function serverProviders()
    {
        return $this->hasMany(ServerProvider::class);
    }

    public function sourceProviders()
    {
        return $this->hasMany(SourceProvider::class);
    }

    public function sshKeys()
    {
        return $this->hasMany(SshKey::class);
    }

    public function provisionServer(ServerProvider $provider, string $name, string $size, string $region)
    {
        $id = $provider->client()->createServer($name, $size, $region);

        return tap($this->servers()->create([
            'name' => $name,
            'server_provider_id' => $provider->id,
            'provider_server_id' => $id,
            'size' => $size,
            'region' => $region,
            'status' => 'creating',
            'username' => 'fuse',
            'sudo_password' => Str::random(40),
            'database_password' => Str::random(40),
        ]))->provision();
    }

    /**
     * Get the user's initials.
     */
    public function initials(): string
    {
        return Str::of($this->name)
            ->explode(' ')
            ->map(fn (string $name) => Str::of($name)->substr(0, 1))
            ->implode('');
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    protected function keypair(): Attribute
    {
        return Attribute::make(
            set: function (object $value) {
                return [
                    'public_key' => $value->publicKey,
                    'private_key' => $value->privateKey,
                ];
            },
        );
    }

    public function keyPath()
    {
        return SecureShellKey::storeFor($this);
    }
}
