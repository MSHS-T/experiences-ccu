<?php

namespace Database\Seeders\Test;

use Illuminate\Database\Seeder;

class TestDatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->command->info("Current environment is not production, seeding test data.");
        $this->call(UsersTableSeeder::class);
        $this->call(EquipmentsTableSeeder::class);
        $this->call(PlateauxTableSeeder::class);
        // $this->call(ManipulationsTableSeeder::class);
    }
}
