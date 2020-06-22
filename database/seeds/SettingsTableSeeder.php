<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SettingsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = [
            ['name' => 'booking_cancellation_delay', 'value' => '1'],
            ['name' => 'booking_confirmation_delay', 'value' => '24'],
            ['name' => 'booking_opening_delay', 'value' => '15'],
            ['name' => 'manipulation_overbooking', 'value' => '120'],
        ];
        $this->command->info("Seeding " . count($data) . " Settings...");
        DB::table('settings')->insertOrIgnore($data);
        $this->command->info("Settings seeded.");
    }
}
