<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('general.booking_cancellation_delay', 1);
        $this->migrator->add('general.booking_confirmation_delay', 24);
        $this->migrator->add('general.booking_opening_delay', 15);
        $this->migrator->add('general.manipulation_overbooking', 120);
        $this->migrator->add('general.email_reminder_delay', 7);
        $this->migrator->add('general.presentation_text', 'Magna tempor amet ut proident nostrud cillum aute commodo. Veniam dolore non velit adipisicing incididunt eu excepteur incididunt consectetur.<br/>Deserunt eiusmod dolore tempor incididunt sit officia velit enim sit ullamco dolor. Adipisicing incididunt veniam exercitation mollit ea pariatur cillum.');
    }
};
