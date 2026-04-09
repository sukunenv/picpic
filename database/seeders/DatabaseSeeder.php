<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Admin User
        User::updateOrCreate(
            ['email' => env('ADMIN_EMAIL', 'admin@picpic.com')],
            ['name' => 'Admin Picpic', 'password' => bcrypt(env('ADMIN_PASSWORD')), 'is_admin' => true]
        );

        // Test User
        User::updateOrCreate(
            ['email' => 'test@example.com'],
            ['name' => 'Test User', 'password' => bcrypt('password')]
        );

        $this->call(MenuItemSeeder::class);
        $this->call(BannerSeeder::class);
    }
}
