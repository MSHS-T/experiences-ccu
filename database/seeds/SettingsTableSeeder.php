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
        DB::table('settings')->insertOrIgnore([
            ['name' => 'manipulation_overbooking', 'value' => '120'],
            ['name' => 'booking_cancellation_delay', 'value' => '1'],
            ['name' => 'booking_opening_delay', 'value' => '15'],
        ]);
    }
}
