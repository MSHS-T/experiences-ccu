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

        $days = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];

        $nbManipulations = 10;

        $this->command->info("Seeding $nbManipulations test manipulations with randomized hours and slot count");
        $this->command->info("Each manipulation will have a random number of slots booked (50-100%)");
        $this->command->info("Each booking will have a 75% chance to be confirmed");
        $this->command->info("Each confirmed booking before the current day will have a 90% chance to be honored");
        $bar = $this->command->getOutput()->createProgressBar($nbManipulations);
        $bar->start();
        foreach (range(1, $nbManipulations) as $i) {
            $description = array_map(function ($p) {
                return '<p>' . $p . '</p>';
            }, $faker->paragraphs(3));

            $hours = [];
            foreach ($days as $day) {
                $hours[$day] = [
                    'enabled'  => false,
                    'am'       => false,
                    'start_am' => '09:00',
                    'end_am'   => '12:00',
                    'pm'       => false,
                    'start_pm' => '14:00',
                    'end_pm'   => '17:00'
                ];
                // 2 chances out of 3 to enable a day (and each half day)
                $hours[$day]['enabled'] = random_int(0, 2) > 0;
                if ($hours[$day]['enabled']) {
                    $hours[$day]['am'] = random_int(0, 2) > 0;
                    if ($hours[$day]['am']) {
                        $hours[$day]['start_am'] = collect(['08:00', '08:30', '09:00', '09:30', '10:00'])->random();
                        $hours[$day]['end_am']   = collect(['11:00', '11:30', '12:00', '12:30'])->random();
                    }
                    $hours[$day]['pm'] = random_int(0, 2) > 0;
                    if ($hours[$day]['pm']) {
                        $hours[$day]['start_pm'] = collect(['13:00', '13:30', '14:00', '14:30', '15:00'])->random();
                        $hours[$day]['end_pm']   = collect(['16:00', '16:30', '17:00', '17:30', '18:00'])->random();
                    }
                }
            }
            $nbSlots = 10 * random_int(5, 15);

            $manip = $plateaux->random()->manipulations()->create([
                'name'            => "Manipulation $i",
                'description'     => implode('', $description),
                'duration'        => 15 * random_int(1, 6),
                'target_slots'    => $nbSlots,
                'start_date'      => '2020-07-01',
                'location'        => 'Toulouse',
                'requirements'    => json_encode($faker->sentences(random_int(1, 5))),
                'available_hours' => json_encode($hours)
            ]);

            $nbManagers = random_int(1, 2);
            $managersId = $managers->random(min(count($managers), $nbManagers))->pluck('id');
            $manip->users()->sync($managersId);

            /**
             * TODO : Uncomment after the slot generation code is integrated
             */
            /*$manip->generateSlots();

            $slots = $manip->slots()->orderBy('start')->get();

            // Random up to 110 to increase the odd of having a fully booked manipulation
            // (1 chance out of 6 to be over 100%)
            $filledSlotsCount = (random_int(50, 110) / 100) * $slots->count();
            if ($filledSlotsCount > $slots->count()) {
                $filledSlotsCount = $slots->count();
            }

            $slots->random($filledSlotsCount)->each(function (Slot $slot) use ($faker) {
                $firstName = $faker->firstName;
                $lastName = $faker->lastName;
                $email = strtolower(Str::slug($firstName) . "." . Str::slug($lastName) . "@" . $faker->freeEmailDomain);

                // 75% chance to be confirmed
                $confirmed = random_int(1, 4) > 1;

                // 90% chance to be honored if before today and confirmed
                // If slot is unconfirmed, 50% chance to be honored
                // If slot is dated today or after, keep null
                $honored = ($slot->start->setTime(0, 0, 0) < Carbon::now()->setTime(0, 0, 0)) ? ($confirmed ? random_int(1, 10) > 1 : boolval(random_int(0, 1))) : null;

                $slot->booking()->create([
                    'first_name'        => $firstName,
                    'last_name'         => $lastName,
                    'email'             => $email,
                    'confirmed'         => $confirmed,
                    'confirmation_code' => $confirmed ? null : Str::uuid(),
                    'confirm_before'    => $confirmed ? null : $faker->dateTimeBetween('-1 days', '+1 days'),
                    'honored'           => $honored
                ]);
            });
            */
            $bar->advance();
        }
        $bar->finish();
        $this->command->line("");
        $this->command->info("Test manipulations seeded.");
    }
}
