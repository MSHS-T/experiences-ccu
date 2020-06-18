<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;


class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(SettingsTableSeeder::class);
        $this->call(RolesTableSeeder::class);
        // Insert admin user
        $adminId = DB::table('users')->insertGetId([
            'first_name' => 'Administrateur',
            'last_name' => 'CCU',
            'email' => (env('APP_ENV', 'production') === 'local' ? 'romain@3rgo.tech' : 'julien.tardieu@univ-tlse2.fr'),
            'email_verified_at' => '2019-10-16 12:00',
            'password' => bcrypt('admin'),
            'created_at' => '2019-10-16 12:00',
            'updated_at' => '2019-10-16 12:00'
        ]);

        DB::table('role_user')->insert([
            'user_id' => $adminId,
            'role_id' => DB::table('roles')->where('name', 'Administrateur')->first()->id
        ]);

        if (env('APP_ENV', 'production') !== 'production') {
            $this->call(TestDatabaseSeeder::class);
        }
    }
}
