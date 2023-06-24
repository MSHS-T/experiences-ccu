<?php

namespace Database\Seeders\Test;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Faker\Factory as Faker;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class UsersTableSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $roles = config('collabccu.roles');
        $faker = Faker::create('fr_FR');
        $this->command->info("Seeding test users with randomized roles.");
        $nbUsers = 10;
        $bar = $this->command->getOutput()->createProgressBar($nbUsers);
        $bar->start();
        foreach (range(1, $nbUsers) as $index) {
            $firstName = $faker->firstName;
            $lastName = $faker->lastName;
            $email = strtolower(Str::slug($firstName) . "." . Str::slug($lastName) . "@" . $faker->freeEmailDomain);
            $user = User::create([
                'first_name'        => $firstName,
                'last_name'         => $lastName,
                'email'             => $email,
                'email_verified_at' => Carbon::now(),
                'password'          => bcrypt('password')
            ]);
            $user->assignRole(collect($roles)->random());
            $bar->advance();
        }
        $bar->finish();
        $this->command->line("");
        $this->command->info("Test users seeded.");
    }
}
