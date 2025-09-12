<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\Manipulation;
use Illuminate\Support\Collection;

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
        $startDate = fake()->dateTimeBetween('-1 months', '+2 months');
        return [
            'name'            => fake()->name,
            'description'     => implode('<br/>', fake()->paragraphs(3)),
            'duration'        => random_int(1, 4) * 30,
            'start_date'      => $startDate,
            'end_date'        => fake()->dateTimeBetween($startDate, '+3 months'),
            'requirements'    => Collection::times(random_int(3, 5), fn() => fake()->sentence())->toJson(),
        ];
    }
}
