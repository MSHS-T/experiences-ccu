<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class TestUsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $roles = [
            'ADMIN',
            'PLAT',
            'MANIP'
        ];
        $faker = Faker::create('fr_FR');
        $this->command->info("Seeding test users with randomized roles.");
        $nbUsers = 10;
        $bar = $this->command->getOutput()->createProgressBar($nbUsers);
        $bar->start();
        foreach (range(1, $nbUsers) as $index) {
            $firstName = $faker->firstName;
            $lastName = $faker->lastName;
            $email = strtolower(Str::slug($firstName) . "." . Str::slug($lastName) . "@" . $faker->freeEmailDomain);
            $userId = DB::table('users')->insertGetId([
                'first_name' => $firstName,
                'last_name' => $lastName,
                'email' => $email,
                'email_verified_at' => '2019-10-16 12:00',
                'password' => bcrypt('password'),
                'created_at' => '2019-10-16 12:00',
                'updated_at' => '2019-10-16 12:00'
            ]);
            DB::table('role_user')->insert([
                'user_id' => $userId,
                'role_id' => DB::table('roles')->where('key', $roles[array_rand($roles)])->first()->id
            ]);
            $bar->advance();
        }
        $bar->finish();
        $this->command->line("");
        $this->command->info("Test users seeded.");
    }
}
