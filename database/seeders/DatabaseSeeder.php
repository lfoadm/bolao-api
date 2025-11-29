<?php

namespace Database\Seeders;

use App\Models\Bet;
use App\Models\Fee;
use App\Models\Game;
use App\Models\Seller;
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
        // User::factory(10)->create();
        
        $this->call([
            UserSeeder::class,
            GameSeeder::class,
            FeeSeeder::class,
            PoolSeeder::class,
            // BetSeeder::class
        ]);

        User::factory(99)->create();
        Bet::factory(100)->create();
    }
}