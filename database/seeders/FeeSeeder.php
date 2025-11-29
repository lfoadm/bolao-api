<?php

namespace Database\Seeders;

use App\Models\Fee;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class FeeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // TAXA PADRÃO DE COMISSÃO DA PLATAFORMA
        Fee::create([
            'platform_fee_percent' => 5,
            'is_active' => true,
        ]);
    }
}
