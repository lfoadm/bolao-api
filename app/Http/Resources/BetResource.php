<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BetResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'pool_id' => $this->pool_id,
            'user_id' => $this->user_id,
            'bet_team_a' => $this->bet_team_a,
            'bet_team_b' => $this->bet_team_b,
            'is_winner' => $this->is_winner,
            'amount_paid' => $this->amount_paid,
            'status' => $this->status,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'winner_value' => $this->winner_value,
            'is_prize_claimed' => $this->is_prize_claimed,

            // Carrega o recurso Pool aninhado
            'pool' => PoolResource::make($this->whenLoaded('pool')),
        ];
    }
}
