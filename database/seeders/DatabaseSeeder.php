<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->withPersonalOrganizationAndSubscription([
            'ssh_public_key' => env('ORG_SSH_PUBLIC_KEY'),
            'ssh_private_key' => env('ORG_SSH_PRIVATE_KEY'),
        ])->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);
    }
}
