<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pool extends Model
{
    /** @use HasFactory<\Database\Factories\PoolFactory> */
    use HasFactory;

    protected $fillable = [
        'seller_id',
        'team_a',
        'team_b',
        'match_date',
        'deadline',
        'score_team_a',
        'score_team_b',
        'title',
        'rules',
        'commission',
        'entry_value',
        'status',
        'theme',
        'is_active',
    ];

    protected $casts = [
        'match_date' => 'datetime',
        'deadline' => 'datetime',
        'is_active' => 'boolean',
    ];

    /**
     * Um Pool pertence a um Seller (criador do bolão).
     */
    public function seller()
    {
        return $this->belongsTo(Seller::class);
    }

    /**
     * Um Pool possui várias apostas (Bets).
     */
    public function bets()
    {
        return $this->hasMany(Bet::class);
    }

    /**
     * Retorna os vencedores do bolão.
     */
    public function winners()
    {
        return $this->bets()->where('is_winner', true);
    }

    /**
     * Verifica o resultado e define vencedores.
     */
    public function checkWinners(): void
    {
        if (is_null($this->score_team_a) || is_null($this->score_team_b)) {
            return; // resultado ainda não informado
        }

        foreach ($this->bets as $bet) {
            $isWinner = (
                $bet->bet_team_a === $this->score_team_a &&
                $bet->bet_team_b === $this->score_team_b
            );

            $bet->update([
                'is_winner' => $isWinner,
                'status' => $isWinner ? 'paid' : 'lost',
            ]);
        }
    }
}
