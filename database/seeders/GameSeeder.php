<?php

namespace Database\Seeders;

use App\Models\Game;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class GameSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Game::create([
            'team_a' => 'Palmeiras',
            'team_b' => 'Flamengo',
            'league' => 'Libertadores',
            'match_datetime' => '2025-11-29 18:00:00'
        ]);

        // Game::create([
        //     'team_a' => 'Brasil',
        //     'team_b' => 'Argentina',
        //     'league' => 'Amistoso',
        //     'match_datetime' => '2025-11-24 19:30:00'
        // ]);

        // Game::create([
        //     'team_a' => 'Palmeiras',
        //     'team_b' => 'Flamengo',
        //     'league' => 'Copa Libertadores',
        //     'match_datetime' => '2025-11-24 19:00:00'
        // ]);

        // Game::create([
        //     'team_a' => 'Corinthians',
        //     'team_b' => 'Mirassol',
        //     'league' => 'Campeonato Brasileiro',
        //     'match_datetime' => '2025-11-24 19:00:00'
        // ]);
    }
}
