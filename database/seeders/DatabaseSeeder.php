<?php

namespace Database\Seeders;

use App\Models\Receipt;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Workspace;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        // User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        // Create 5 users, each with 2 workspaces, each workspace with 10 receipts
        DB::transaction(function () {
            $users = User::factory(5)->create();

            $workspaces = Workspace::factory(10)->create();

            foreach ($workspaces as $workspace) {

                $workspace->users()->attach(
                    $users->random(2)->pluck('id')
                );

                Receipt::factory(10)->create([
                    'workspace_id' => $workspace->id
                ]);
            }
        });
    }
}
