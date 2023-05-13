<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\BookingHistory;

class BookingHistoryFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = BookingHistory::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'hashed_email' => $this->faker->word,
            'booking_made' => $this->faker->randomNumber(),
            'booking_confirmed' => $this->faker->randomNumber(),
            'booking_confirmed_honored' => $this->faker->randomNumber(),
            'booking_unconfirmed_honored' => $this->faker->randomNumber(),
            'blocked' => $this->faker->boolean,
        ];
    }
}
