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
            'duration'        => random_int(1, 6) * 15,
            'start_date'      => $startDate,
            'end_date'        => fake()->dateTimeBetween($startDate, '+3 months'),
            'location'        => fake()->word,
            'available_hours' => json_encode([
                "monday"    => ["end_am" => "12:00", "end_pm" => "17:00", "start_am" => "09:00", "start_pm" => "14:00"],
                "tuesday"   => ["end_am" => "12:00", "end_pm" => "17:00", "start_am" => "09:00", "start_pm" => "14:00"],
                "thursday"  => ["end_am" => "12:00", "end_pm" => "17:00", "start_am" => "09:00", "start_pm" => "14:00"],
                "wednesday" => ["end_am" => "12:00", "end_pm" => "17:00", "start_am" => "09:00", "start_pm" => "14:00"],
                "friday"    => ["end_am" => "12:00", "end_pm" => "17:00", "start_am" => "09:00", "start_pm" => "14:00"],
            ]),
            'requirements'    => Collection::times(random_int(3, 5), fn () => fake()->sentence())->toJson(),
        ];
    }
}
