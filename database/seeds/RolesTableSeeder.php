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
            ['id' => 1, 'name' => 'Administrateur'],
            ['id' => 2, 'name' => 'Responsable Plateau'],
            ['id' => 3, 'name' => 'Responsable Manipulation'],
        ]);
    }
}
