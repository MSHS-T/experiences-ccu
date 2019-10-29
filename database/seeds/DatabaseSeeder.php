<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // Insert admin user
        DB::table('users')->insert([
            'first_name' => 'Administrateur',
            'last_name' => 'CCU',
            'email' => 'romain@3rgo.tech',
            'email_verified_at' => '2019-10-16 12:00',
            'password' => bcrypt('admin'),
            'created_at' => '2019-10-16 12:00',
            'updated_at' => '2019-10-16 12:00'
        ]);

        // $this->call(UsersTableSeeder::class);
    }
}
