<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bet extends Model
{
    /** @use HasFactory<\Database\Factories\BetFactory> */
    use HasFactory;

    protected $fillable = [
        'pool_id',
        'user_id',
        'bet_team_a',
        'bet_team_b',
        'is_winner',
        'is_paid',
        'amount_paid',
        'winner_value',
        'is_prize_claimed',
    ];

    protected $casts = [
        'amount_paid' => 'float',
        'winner_value' => 'float',
        'is_winner' => 'boolean',
        'is_paid' => 'boolean',
        'is_prize_claimed' => 'boolean',
    ];

    // -----------------------------------
    // RELACIONAMENTOS
    // -----------------------------------

    public function pool()
    {
        return $this->belongsTo(Pool::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // -----------------------------------
    // MÉTODOS ÚTEIS
    // -----------------------------------

    public function belongsToFinishedMatch(): bool
    {
        return $this->pool->game->is_finished;
    }

    public function checkIfWinner(): bool
    {
        $game = $this->pool->game;

        if (!$game->is_finished) {
            return false;
        }

        return 
            $this->bet_team_a == $game->final_score_a &&
            $this->bet_team_b == $game->final_score_b;
    }
}
