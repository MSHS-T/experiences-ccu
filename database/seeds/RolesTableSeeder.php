<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RolesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = [
            ['id' => 1, 'key' => 'ADMIN', 'name' => 'Administrateur'],
            ['id' => 2, 'key' => 'PLAT',  'name' => 'Responsable Plateau'],
            ['id' => 3, 'key' => 'MANIP', 'name' => 'Responsable Manipulation'],
        ];

        $this->command->info("Seeding " . count($data) . " Roles...");
        DB::table('roles')->insertOrIgnore($data);
        $this->command->info("Roles seeded.");
    }
}
