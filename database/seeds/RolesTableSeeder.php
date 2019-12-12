<?php

use Illuminate\Database\Seeder;

class RolesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('roles')->insertOrIgnore([
            ['id' => 1, 'key' => 'ADMIN', 'name' => 'Administrateur'],
            ['id' => 2, 'key' => 'PLAT',  'name' => 'Responsable Plateau'],
            ['id' => 3, 'key' => 'MANIP', 'name' => 'Responsable Manipulation'],
        ]);
    }
}
