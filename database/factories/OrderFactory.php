<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Order>
 */
class OrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'table_id' => \App\Models\Table::factory(),
            'reservation_id' => \App\Models\Reservation::factory(),
            'customer_id' => \App\Models\User::factory(),
            'user_id' => \App\Models\User::factory(),
            'total' => $this->faker->randomFloat(2, 50, 200),
            'paid' => false,
            'date' => $this->faker->dateTime,
        ];
    }
}
