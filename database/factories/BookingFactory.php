<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\Booking;

class BookingFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Booking::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'first_name' => $this->faker->firstName,
            'last_name' => $this->faker->lastName,
            'email' => $this->faker->safeEmail,
            'confirmed' => false,
            'confirmation_code' => $this->faker->uuid(),
            'confirm_before' => $this->faker->dateTimeInInterval('now', '+30 days'),
            'honored' => $this->faker->boolean,
        ];
    }

    /**
     * Indicate that the booking should be confirmed.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'confirmed' => true,
        ]);
    }
}
