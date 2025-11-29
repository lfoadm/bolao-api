<?php

namespace Database\Seeders;

use App\Models\Bet;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BetSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Bet::create([
            'pool_id' => 1,
            'user_id' => 2,
            'bet_team_a' => 0,
            'bet_team_b' => 0,
            'amount_paid' => 1000,
            'status' => 'paid',
        ]);
    }
}
