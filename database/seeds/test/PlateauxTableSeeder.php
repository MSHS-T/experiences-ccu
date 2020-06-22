<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
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

        $this->command->info("Seeding test plateaux with randomized manager.");
        $nbPlateaux = 10;
        $bar = $this->command->getOutput()->createProgressBar($nbPlateaux);
        $bar->start();
        foreach (range(1, $nbPlateaux) as $index) {
            $description = array_map(function ($p) {
                return '<p>' . $p . '</p>';
            }, $faker->paragraphs(3));
            DB::table('plateaux')->insert([
                'name' => "Plateau $index",
                'description' => implode('', $description),
                'manager_id' => $managers->random(),
                'created_at' => '2020-01-12 12:00',
                'updated_at' => '2020-01-12 12:00'
            ]);
            $bar->advance();
        }
        $bar->finish();
        $this->command->line("");
        $this->command->info("Test plateaux seeded.");
    }
}
