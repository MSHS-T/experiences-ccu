<?php

use App\Manipulation;
use App\Slot;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class TestManipulationsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $role_id = DB::table('roles')->where('key', 'MANIP')->first()->id;
        $managers = DB::table('role_user')->select('id')->where('role_id', $role_id)->get()->pluck('id');
        $plateaux = DB::table('plateaux')->select('id')->get()->pluck('id')->toArray();
        $faker = Faker::create('fr_FR');

        $days = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];

        $nbManipulations = 10;

        $this->command->info("Seeding $nbManipulations test manipulations with randomized hours and slot count");
        $this->command->info("Each manipulation will have a random number of slots booked (50-100%)");
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
                $hours[$day]['enabled'] = boolval(random_int(0, 1));
                if ($hours[$day]['enabled']) {
                    $hours[$day]['am'] = boolval(random_int(0, 1));
                    if ($hours[$day]['am']) {
                        $hours[$day]['start_am'] = collect(['08:00', '08:30', '09:00', '09:30', '10:00'])->random();
                        $hours[$day]['end_am'] = collect(['11:00', '11:30', '12:00', '12:30'])->random();
                    }
                    $hours[$day]['pm'] = boolval(random_int(0, 1));
                    if ($hours[$day]['pm']) {
                        $hours[$day]['start_pm'] = collect(['13:00', '13:30', '14:00', '14:30', '15:00'])->random();
                        $hours[$day]['end_pm'] = collect(['16:00', '16:30', '17:00', '17:30', '18:00'])->random();
                    }
                }
            }
            $nbSlots = 10 * random_int(5, 15);

            $manipId = DB::table('manipulations')->insertGetId([
                'name' => "Manipulation $i",
                'description' => implode('', $description),
                'plateau_id' => $plateaux[array_rand($plateaux)],
                'duration' => 15 * random_int(1, 6),
                'target_slots' => $nbSlots,
                'start_date' => '2020-07-01',
                'location' => 'Toulouse',
                'requirements' => json_encode($faker->sentences(random_int(1, 5))),
                'available_hours' => json_encode($hours),
                'created_at' => '2020-06-22 12:00',
                'updated_at' => '2020-06-22 12:00'
            ]);

            $nbManagers = random_int(1, 2);
            $managers->random($nbManagers)->each(function ($managerId) use ($manipId) {
                DB::table('manipulation_user')->insert([
                    'user_id'         => $managerId,
                    'manipulation_id' => $manipId
                ]);
            });

            $manipulation = Manipulation::find($manipId);
            $manipulation->generateSlots();

            $slots = $manipulation->slots()->orderBy('start')->get();

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

                $slot->fill([
                    'subject_first_name' => $firstName,
                    'subject_last_name' => $lastName,
                    'subject_email' => $email,
                    'subject_confirmed' => $confirmed,
                    'subject_confirmation_code' => $confirmed ? null : Str::uuid()
                ]);
                $slot->save();
            });

            $bar->advance();
        }
        $bar->finish();
        $this->command->line("");
        $this->command->info("Test manipulations seeded.");
    }
}
