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
        $names = [
            'BabyLab'     => 'recherches sur le développement, les capacités sensori-motrices, cognitives et communicatives des enfants',
            'CLOE'        => 'plateforme de mesures comportementales liées à la perception de productions langagières',
            'PACOMP'      => 'système d’acquisition de données physiologiques',
            'ImNum'       => 'Composante portative d’acquisition d’images numériques',
            'OCULOMÉTRIE' => 'systèmes d’analyse de mouvements oculaires',
            'PETRA'       => 'recherche sur la perception du son',
            'ROB'         => 'étude de l’interaction Homme-Robot',
            'SIMULAUTO'   => 'simulateur de conduite automobile',
            'TAB'         => 'étude des réponses comportementales au niveau des apprentissages scolaires et au niveau des processus cognitifs liée au vieillissement',
        ];

        $managers = User::role('plateau_manager')->get();
        $equipments = Equipment::all();
        $faker = Faker::create('fr_FR');

        $this->command->info("Seeding plateaux with randomized manager.");
        $bar = $this->command->getOutput()->createProgressBar(count($names));
        $bar->start();
        foreach ($names as $name => $description) {
            $plateau = Plateau::make([
                'name'        => $name,
                'description' => '<p>' . $description . '</p>'
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
