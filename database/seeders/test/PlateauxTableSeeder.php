<?php

namespace Database\Seeders\Test;

use App\Models\Equipment;
use App\Models\Plateau;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class PlateauxTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $managers = User::role('plateau_manager')->get();
        $equipments = Equipment::all();
        $faker = Faker::create('fr_FR');

        $this->command->info("Seeding test plateaux with randomized manager.");
        $nbPlateaux = 10;
        $bar = $this->command->getOutput()->createProgressBar($nbPlateaux);
        $bar->start();
        foreach (range(1, $nbPlateaux) as $index) {
            $description = array_map(function ($p) {
                return '<p>' . $p . '</p>';
            }, $faker->paragraphs(3));
            $plateau = Plateau::make([
                'name'        => "Plateau $index",
                'description' => implode('', $description)
            ]);
            /** @var Plateau $plateau */
            $plateau->manager()->associate($managers->random());
            $plateau->save();

            $plateau_equipments = $equipments->random(random_int(1, 3))
                ->mapWithKeys(fn (Equipment $equipment) => [$equipment->id => ['quantity' => random_int(1, $equipment->quantity)]])
                ->all();

            $plateau->equipments()->sync($plateau_equipments);
            $bar->advance();
        }
        $bar->finish();
        $this->command->line("");
        $this->command->info("Test plateaux seeded.");
    }
}
