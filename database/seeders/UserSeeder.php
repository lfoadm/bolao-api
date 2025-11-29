<?php

namespace Database\Seeders;

use App\Models\Seller;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // ADMINISTRADOR DO SISTEMA
        $admin = User::factory()->create([
            'name' => 'Leandro',
            'phone' => '1',
            'type' => 'admin'
        ]);

        // Usuário Seller, que será o SELLER
        $seller = User::factory()->create([
            'name' => 'SELLER',
            'phone' => '123456',
            'type' => 'seller'
        ]);

        // Transformando o usuário 2 em SELLER
        Seller::create([
            'user_id' => $seller->id,
            'store_name' => 'VIP CONVENIÊNCIA',
            'bio' => 'biografia completa',
            'pix_key' => '123456',
        ]);
    }
}
