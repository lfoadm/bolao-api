<?php

namespace Database\Seeders;

use App\Models\Pool;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PoolSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Pool::create([
            'seller_id' => 1,
            'game_id' => 1,
            'entry_value' => 20.00,
            'platform_fee' => 5,
            'title' => 'BOLÃO DA VIP',
            'rules' => '
                        ------ Premiação ------
                        🏆 Quem acertar o placar exato leva o prêmio!
                        💸 Se houver mais de um vencedor, o prêmio será dividido igualmente.
                        🤷‍♂️ Se ninguém acertar, o organizador é o vencedor do prêmio.

                        --- Transparência ---
                        📋 Todos os palpites serão registrados antes da partida.
                        📢 O resultado será divulgado após o fim do jogo.

                        --- Regras Extras ---
                        ⚽ Vale somente o placar no tempo normal, sem prorrogação/pênaltis.
                        ',
            'status' => 'open',
        ]);

        // Pool::create([
        //     'seller_id' => 1,
        //     'game_id' => 1,
        //     'entry_value' => 1000.00,
        //     'commission' => 10,
        //     'platform_fee' => 5,
        //     'title' => 'JOGO 1',
        //     'rules' => 'MINHAS REGRAS',
        //     'status' => 'open',
        // ]);

        // Pool::create([
        //     'seller_id' => 1,
        //     'game_id' => 2,
        //     'entry_value' => 150.00,
        //     'commission' => 15,
        //     'platform_fee' => 5,
        //     'title' => 'JOGO 2',
        //     'rules' => 'MINHAS REGRAS',
        //     'status' => 'open',
        // ]);

        // Pool::create([
        //     'seller_id' => 1,
        //     'game_id' => 3,
        //     'entry_value' => 200.00,
        //     'commission' => 20,
        //     'platform_fee' => 5,
        //     'title' => 'JOGO 3',
        //     'rules' => 'MINHAS REGRAS',
        //     'status' => 'open',
        // ]);
    }
}
