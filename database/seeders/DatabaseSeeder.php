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
        User::factory()->create([
            'name'     => 'Admin Picpic',
            'email'    => 'admin@picpic.com',
            'password' => bcrypt('picpic123'),
            'is_admin' => true,
        ]);

        // Test User
        User::factory()->create([
            'name'  => 'Test User',
            'email' => 'test@example.com',
        ]);

        $this->call(MenuItemSeeder::class);
    }
}
