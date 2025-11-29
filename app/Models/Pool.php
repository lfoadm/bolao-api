<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pool extends Model
{
    /** @use HasFactory<\Database\Factories\PoolFactory> */
    use HasFactory;

    protected $fillable = [
        'game_id',
        'seller_id',
        'entry_value',
        'commission',
        'platform_fee',
        'is_active',
        'title',
        'status',
        'winner_count',
        'total_bets',
    ];

    protected $casts = [
        'entry_value' => 'decimal:2',
        'commission' => 'decimal:2',
        'platform_fee' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    protected $appends = [
        'current_prize'
    ];

    // -----------------------------------
    // RELACIONAMENTOS
    // -----------------------------------

    public function game()
    {
        return $this->belongsTo(Game::class);
    }

    public function seller()
    {
        return $this->belongsTo(Seller::class); // ou User, dependendo
    }

    public function bets()
    {
        return $this->hasMany(Bet::class);
    }

    // -----------------------------------
    // ACESSOR DO PRÊMIO ATUALIZADO
    // -----------------------------------

    public function getCurrentPrizeAttribute()
    {
        $totalPalpites = $this->bets()->count(); //2
        $valorCota = $this->entry_value;
        
        $totalArrecadado = $totalPalpites * $valorCota;

        $commissionSeller = $this->commission / 100; // Converte comissão percentual (ex: 15 vira 0.15)
        $commissionPlatform = $this->platform_fee / 100; // Converte comissão percentual (ex: 15 vira 0.15)

        // Calcula valores
        $commissionSellerAmount     = $totalArrecadado * $commissionSeller;
        $commissionPlatformAmount   = $totalArrecadado * $commissionPlatform;

        // Fórmula final do prêmio líquido
        $netPrize = max(0, 
            $totalArrecadado - ($commissionSellerAmount + $commissionPlatformAmount)
        );

        return round($netPrize, 2);
    }

    public function getDeadlineAttribute()
    {
        // Se não houver jogo associado, evita erro
        if (!$this->game || !$this->game->match_datetime) {
            return null;
        }

        // Calcula 15 min antes do horário da partida
        return \Illuminate\Support\Carbon::parse($this->game->match_datetime)
            ->subMinutes(15);
    }

    // -----------------------------------
    // STATUS
    // -----------------------------------

    public function isOpen(): bool
    {
        return $this->is_active && !$this->game->isMatchFinished();
    }

    public function isFinished(): bool
    {
        return !$this->is_active || $this->game->isMatchFinished();
    }
}
