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
        $user = $this->seedUser();
    }

    /**
     * Seed the test user with personal organization and subscription.
     */
    private function seedUser(): User
    {
        return User::factory()->withPersonalOrganizationAndSubscription()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);
    }
}
