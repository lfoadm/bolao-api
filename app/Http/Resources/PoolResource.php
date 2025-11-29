<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PoolResource extends JsonResource
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
            'seller_id' => $this->seller_id,
            'game_id' => $this->game_id,
            'entry_value' => number_format($this->entry_value, 2, ',', '.'),
            'commission' => $this->commission,
            'platform_fee' => $this->platform_fee,
            'title' => $this->title,
            'rules' => $this->rules,
            'status' => $this->status,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'current_prize' => $this->current_prize, // Accessor
            'winner_count' => $this->winner_count,
            
            // Carrega os recursos aninhados
            'game' => GameResource::make($this->whenLoaded('game')),
            'seller' => SellerResource::make($this->whenLoaded('seller')),
        ];
    }
}
