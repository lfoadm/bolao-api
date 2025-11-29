<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GameResource extends JsonResource
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
            'team_a' => $this->team_a,
            'team_b' => $this->team_b,
            'league' => $this->league,
            'match_datetime' => $this->match_datetime,
            'final_score_a' => $this->final_score_a,
            'final_score_b' => $this->final_score_b,
            'is_finished' => $this->is_finished,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
