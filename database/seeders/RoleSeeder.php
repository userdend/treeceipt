<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Role::insert([
            [
                'name' => 'admin',
                'display_name' => 'Admin',
                'description' => 'Administrator with full access.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'user',
                'display_name' => 'User',
                'description' => 'Regular user account.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
