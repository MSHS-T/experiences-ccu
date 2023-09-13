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
        $this->command->info("Seeding test users");
        $nbRespPlateau = 3;
        $nbRespManip = 5;
        $bar = $this->command->getOutput()->createProgressBar($nbRespPlateau + $nbRespManip);
        $bar->start();
        foreach (range(1, $nbRespPlateau) as $index) {
            $firstName = 'Resp';
            $lastName = 'Plateau ' . ($index);
            $email = strtolower(Str::slug($firstName) . "." . Str::slug($lastName) . "@univ-tlse2.fr");
            $user = User::create([
                'first_name'        => $firstName,
                'last_name'         => $lastName,
                'email'             => $email,
                'email_verified_at' => Carbon::now(),
                'password'          => bcrypt('password')
            ]);
            $user->assignRole('plateau_manager');
            $bar->advance();
        }
        foreach (range(1, $nbRespManip) as $index) {
            $firstName = 'Resp';
            $lastName = 'Manip ' . ($index);
            $email = strtolower(Str::slug($firstName) . "." . Str::slug($lastName) . "@univ-tlse2.fr");
            $user = User::create([
                'first_name'        => $firstName,
                'last_name'         => $lastName,
                'email'             => $email,
                'email_verified_at' => Carbon::now(),
                'password'          => bcrypt('password')
            ]);
            $user->assignRole('manipulation_manager');
            $bar->advance();
        }
        $bar->finish();
        $this->command->line("");
        $this->command->info("Test users seeded.");
    }
}
