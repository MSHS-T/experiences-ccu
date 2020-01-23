<?php

use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class TestPlateauxTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $role_id = DB::table('roles')->where('key', 'PLAT')->first()->id;
        $managers = DB::table('role_user')->select('id')->where('role_id', $role_id)->get()->pluck('id');
        $faker = Faker::create('fr_FR');
        foreach (range(1, 10) as $i) {
            $description = array_map(function($p) { return '<p>'.$p.'</p>'; }, $faker->paragraphs(3));
            DB::table('plateaux')->insert([
                'name' => "Plateau $i",
                'description' => implode('', $description),
                'manager_id' => $managers->random(),
                'created_at' => '2020-01-12 12:00',
                'updated_at' => '2020-01-12 12:00'
            ]);
        }
    }
}
