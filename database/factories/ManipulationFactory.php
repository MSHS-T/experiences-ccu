<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\Manipulation;

class ManipulationFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Manipulation::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->name,
            'description' => $this->faker->text,
            'duration' => $this->faker->randomNumber(),
            'target_slots' => $this->faker->randomNumber(),
            'start_date' => $this->faker->date(),
            'location' => $this->faker->word,
            'available_hours' => '{}',
            'requirements' => '{}',
        ];
    }
}
