<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class TestEquipmentsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $names = [
            'EEG' => 'Mesure',
            'ECG' => 'Mesure',
            'Tensiomètre' => 'Mesure',
            'Spiromètre' => 'Mesure',
            'Thermomètre' => 'Mesure',
            'Projecteur' => 'Environnement',
            'Simulateur de conduite' => 'Environnement',
            'Tapis de course' => 'Environnement',
            'Caméra' => 'Enregistrement',
            'Microphone' => 'Enregistrement'
        ];
        $faker = Faker::create('fr_FR');

        $this->command->info("Seeding test equipments.");
        $bar = $this->command->getOutput()->createProgressBar(count($names));
        $bar->start();
        foreach ($names as $name => $type) {
            $description = array_map(function ($p) {
                return '<p>' . $p . '</p>';
            }, $faker->paragraphs(3));
            DB::table('equipments')->insert([
                'name' => $name,
                'type' => $type,
                'description' => implode('', $description),
                'quantity' => $faker->numberBetween(1, 20),
                'created_at' => '2019-12-12 12:00',
                'updated_at' => '2019-12-12 12:00'
            ]);
            $bar->advance();
        }
        $bar->finish();
        $this->command->line("");
        $this->command->info("Test equipments seeded.");
    }
}
