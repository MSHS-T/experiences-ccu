<?php

use Illuminate\Database\Seeder;
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
        foreach ($names as $name => $type) {
            DB::table('equipments')->insert([
                'name' => $name,
                'type' => $type,
                'description' => $faker->text(2000),
                'quantity' => $faker->numberBetween(1,20),
                'created_at' => '2019-12-12 12:00',
                'updated_at' => '2019-12-12 12:00'
            ]);
        }
    }
}
