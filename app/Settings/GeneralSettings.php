<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class GeneralSettings extends Settings
{
    public int $booking_cancellation_delay;
    public int $booking_confirmation_delay;
    public int $booking_opening_delay;
    public int $email_reminder_delay;
    public string $presentation_text;
    public string $access_instructions;

    public static function group(): string
    {
        return 'general';
    }
}
