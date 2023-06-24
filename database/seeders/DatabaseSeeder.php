<?php

namespace Database\Seeders;

use App\Models\User;
use Carbon\Carbon;
use Database\Seeders\Test\TestDatabaseSeeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->command->info("Starting database Seed...");

        $this->call(RolesPermissionsSeeder::class);
        // $this->call(SettingsTableSeeder::class);

        $this->command->info('Creating Admin user');
        $owner = User::create([
            'first_name'        => 'Administrateur',
            'last_name'         => 'CCU',
            'email'             => (App::isLocal() ? 'romain@3rgo.tech' : 'julien.tardieu@univ-tlse2.fr'),
            'email_verified_at' => Carbon::now(),
            'password'          => Hash::make('password'),
        ]);
        $owner->assignRole('administrator');


        if (!App::isProduction()) {
            $this->call(TestDatabaseSeeder::class);
        }
    }
}
