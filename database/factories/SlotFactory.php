<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\Slot;

class SlotFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Slot::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'start' => $this->faker->dateTime(),
            'end' => $this->faker->dateTime(),
        ];
    }
}
