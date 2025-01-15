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

        $days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];

        $nbManipulations = 10;

        $this->command->info("Seeding $nbManipulations test manipulations with randomized hours");
        $bar = $this->command->getOutput()->createProgressBar($nbManipulations);
        $bar->start();
        foreach (range(1, $nbManipulations) as $i) {
            $description = array_map(function ($p) {
                return '<p>' . $p . '</p>';
            }, $faker->paragraphs(1));

            if ($i === 1) {
                $startDate = fake()->dateTimeBetween('-4 months', '-2 months');
                $endDate = fake()->dateTimeBetween('-2 months', 'now');
            } else if ($i <= 4) {
                $startDate = fake()->dateTimeBetween('-2 months', 'now');
                $endDate = fake()->dateTimeBetween('now', '+2 months');
            } else {
                $startDate = fake()->dateTimeBetween('now', '+2 months');
                $endDate = $startDate->add(new \DateInterval('P' . random_int(2, 6) * 7 . 'D'));
            }

            $manip = $plateaux->random()->manipulations()->create([
                'name'            => "Manipulation $i",
                'description'     => implode('', $description),
                'duration'        => 15 * random_int(1, 6),
                'start_date'      => $startDate->format('Y-m-d'),
                'end_date'        => $endDate->format('Y-m-d'),
                'requirements'    => $faker->sentences(random_int(1, 5)),
            ]);

            if ($i <= 4) {
                $manip->published = true;
                $manip->save();
                $manip->createOrUpdateSlots();
            }

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
