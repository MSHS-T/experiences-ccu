<?php

namespace Database\Seeders\Test;

use App\Models\Manipulation;
use App\Models\Plateau;
use App\Models\Slot;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class ManipulationsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $managers = User::role('manipulation_manager')->get();
        $plateaux = Plateau::all();
        $faker = Faker::create('fr_FR');

        $days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday'];

        $nbManipulations = 10;

        $this->command->info("Seeding $nbManipulations test manipulations with randomized hours");
        $bar = $this->command->getOutput()->createProgressBar($nbManipulations);
        $bar->start();
        foreach (range(1, $nbManipulations) as $i) {
            $description = array_map(function ($p) {
                return '<p>' . $p . '</p>';
            }, $faker->paragraphs(3));

            $hours = [];
            foreach ($days as $day) {
                // 3 chances out of 4 to enable a day
                if (random_int(0, 3) > 0) {
                    $hours[$day] = [
                        'start_am' => collect(['08:00', '08:30', '09:00', '09:30', '10:00'])->random(),
                        'end_am'   => collect(['11:00', '11:30', '12:00', '12:30'])->random(),
                        'start_pm' => collect(['13:00', '13:30', '14:00', '14:30', '15:00'])->random(),
                        'end_pm'   => collect(['16:00', '16:30', '17:00', '17:30', '18:00'])->random()
                    ];
                }
            }

            $startDate = fake()->dateTimeBetween('now', '+2 months');

            $manip = $plateaux->random()->manipulations()->create([
                'name'            => "Manipulation $i",
                'description'     => implode('', $description),
                'duration'        => 15 * random_int(1, 6),
                'start_date'      => $startDate,
                'end_date'        => $startDate->add(new \DateInterval('P' . random_int(1, 3) . 'W')),
                'location'        => 'Toulouse',
                'requirements'    => $faker->sentences(random_int(1, 5)),
                'available_hours' => $hours
            ]);

            $nbManagers = random_int(1, 2);
            $managersId = $managers->random(min(count($managers), $nbManagers))->pluck('id');
            $manip->users()->sync($managersId);
            $bar->advance();
        }
        $bar->finish();
        $this->command->line("");
        $this->command->info("Test manipulations seeded.");
    }
}
