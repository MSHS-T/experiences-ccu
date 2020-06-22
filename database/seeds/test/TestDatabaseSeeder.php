<?php

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
        $this->call(TestUsersTableSeeder::class);
        $this->call(TestEquipmentsTableSeeder::class);
        $this->call(TestPlateauxTableSeeder::class);
        $this->call(TestManipulationsTableSeeder::class);
    }
}
