<?php

namespace Database\Seeders\Test;

use App\Models\Manipulation;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

class TestDatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->command->info("Current environment is not production, seeding test data.");
        $this->call(UsersTableSeeder::class);
        $this->call(EquipmentsTableSeeder::class);
        $this->call(PlateauxTableSeeder::class);

        $respPlateau = User::role('plateau_manager')
            ->get()
            ->filter(fn (User $user) => $user->plateaux->isNotEmpty())
            ->random();
        $respManip = User::role('manipulation_manager')
            ->get()
            ->random();
        $plateau  = $respPlateau->plateaux->random();
        $start    = now()->startOfWeek(1)->addWeek();
        $end      = $start->clone()->addWeeks(2);
        $halfDays = collect(['monday', 'tuesday', 'wednesday', 'thursday', 'friday'])
            ->crossJoin(['am', 'pm'])
            ->map(fn ($item) => $item[0] . '_' . $item[1])
            ->random(5);
        $plateau->attributions()->create([
            'manipulation_manager_id' => $respManip->id,
            'creator_id'              => $respPlateau->id,
            'start_date'              => $start,
            'end_date'                => $end,
            'allowed_halfdays'        => $halfDays->all(),
        ]);

        $manipulation = new Manipulation([
            'plateau_id'      => $plateau->id,
            'name'            => fake()->words(3, true),
            'description'     => fake()->words(15, true),
            'duration'        => 60,
            'start_date'      => $start,
            'end_date'        => $end,
            'available_hours' => $halfDays
                ->map(fn ($hd) => explode('_', $hd))
                ->reduce(function ($all, $day) {
                    Arr::add($all, $day[0], []);
                    Arr::set($all, $day[0] . '.start_' . $day[1], config('collabccu.default_hours.start_' . $day[1]));
                    Arr::set($all, $day[0] . '.end_' . $day[1], config('collabccu.default_hours.end_' . $day[1]));
                    return $all;
                }, []),
            'requirements' => Collection::times(3)->map(fn () => fake()->words(3, true))->all(),
            'published'    => true,
        ]);
        $manipulation->save();
        $manipulation->users()->attach($respManip->id);
        $manipulation->createOrUpdateSlots();
    }
}
