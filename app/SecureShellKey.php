<?php

namespace App;

use App\Models\User;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Str;

class SecureShellKey
{
    public static function forNewUser()
    {
        return app()->environment(/*'local',*/ 'testing')
            ? static::forTesting()
            : static::make();
    }

    protected static function forTesting()
    {
        return (object) [
            'publicKey' => file_get_contents(env('TEST_SSH_PUBLIC_KEY')),
            'privateKey' => file_get_contents(env('TEST_SSH_PRIVATE_KEY')),
        ];
    }

    public static function make()
    {
        $name = Str::random(20);

        Process::path(storage_path('app'))->run(
            "ssh-keygen -C \"robot@terrific.com.mx\" -f {$name} -t ed25519 -N ''"
        );

        [$publicKey, $privateKey] = [
            file_get_contents(storage_path('app/'.$name.'.pub')),
            file_get_contents(storage_path('app/'.$name)),
        ];

        @unlink(storage_path('app/'.$name.'.pub'));
        @unlink(storage_path('app/'.$name));

        return (object) compact('publicKey', 'privateKey');
    }

    public static function storeFor(User $user)
    {
        return tap(storage_path('app/keys/'.$user->id), function ($path) use ($user) {
            static::ensureKeyDirectoryExists();

            static::ensureFileExists($path, $user->private_key, 0600);
        });
    }

    protected static function ensureKeyDirectoryExists()
    {
        if (! is_dir(storage_path('app/keys'))) {
            mkdir(storage_path('app/keys'), 0755, true);
        }
    }

    protected static function ensureFileExists($path, $contents, $chmod)
    {
        file_put_contents($path, $contents);

        chmod($path, $chmod);
    }
}
