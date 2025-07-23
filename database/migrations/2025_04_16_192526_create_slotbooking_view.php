<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement($this->dropView());
        DB::statement($this->createView());
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement($this->dropView());
    }

    private function createView(): string
    {
        return <<<SQL
CREATE VIEW slotbooking_view AS
SELECT
    CONCAT(slots.id, "-", COALESCE(bookings.id, 0)) AS id,
    slots.id AS slot_id,
    bookings.id AS booking_id,
    slots.manipulation_id AS manipulation_id,
    slots.start AS start,
    slots.end AS end,
    bookings.last_name AS last_name,
    bookings.first_name AS first_name,
    bookings.email AS email,
    bookings.confirmed AS confirmed,
    bookings.honored AS honored
FROM
    slots
    LEFT JOIN bookings ON slots.id = bookings.slot_id
ORDER BY
    slots.start,
    bookings.last_name,
    bookings.first_name
SQL;
    }

    private function dropView(): string
    {
        return "DROP VIEW IF EXISTS slotbooking_view";
    }
};
