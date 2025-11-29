<?php

namespace Database\Factories;

use App\Models\Bet;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Bet>
 */
class BetFactory extends Factory
{
    /**
     * O nome do Model correspondente.
     *
     * @var string
     */
    protected $model = Bet::class;

    public function definition(): array
    {
        return [
            'pool_id' => 1,
            'user_id' => $this->faker->numberBetween(3, 101),

            'bet_team_a' => $this->faker->numberBetween(0, 9),
            'bet_team_b' => $this->faker->numberBetween(0, 9),

            'is_winner' => false,
            'amount_paid' => 20,
            'winner_value' => 0,

            'status' => 'paid',
            'is_prize_claimed' => false,
        ];
    }
}