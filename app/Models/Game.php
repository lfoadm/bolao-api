<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Game extends Model
{
    /** @use HasFactory<\Database\Factories\GameFactory> */
    use HasFactory;

    protected $fillable = [
        'team_a',
        'team_b',
        'league',
        'match_datetime',
        'final_score_a',
        'final_score_b',
        'is_finished',
    ];

    protected $casts = [
        'match_datetime' => 'datetime',
        'is_finished' => 'boolean',
    ];

    // -----------------------------------
    // RELACIONAMENTOS
    // -----------------------------------

    public function pools()
    {
        return $this->hasMany(Pool::class);
    }

    // -----------------------------------
    // MÉTODOS ÚTEIS
    // -----------------------------------

    public function isMatchFinished(): bool
    {
        return $this->is_finished === true;
    }

    public function getWinnerAttribute()
    {
        if (!$this->is_finished) {
            return null;
        }

        if ($this->final_score_a > $this->final_score_b) {
            return 'A';
        }
        if ($this->final_score_b > $this->final_score_a) {
            return 'B';
        }
        return 'draw';
    }
}
