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
        'amount_paid',
        'status',
    ];

    protected $casts = [
        'is_winner' => 'boolean',
        'amount_paid' => 'decimal:2',
    ];

    /**
     * A aposta pertence a um Pool.
     */
    public function pool()
    {
        return $this->belongsTo(Pool::class);
    }

    /**
     * A aposta pertence a um Usuário (Consumer).
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
