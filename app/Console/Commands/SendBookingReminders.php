<?php

namespace App\Console\Commands;

use App\Actions\CancelBookingAction;
use App\Mail\BookingReminder;
use App\Models\Slot;
use App\Settings\GeneralSettings;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use MagicLink\MagicLink;

class SendBookingReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:send-booking-reminders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $settings = app(GeneralSettings::class);
        $firstReminderDelay = $settings->email_first_reminder_delay;
        $lastReminderDelay = $settings->email_last_reminder_delay;

        $slots = Slot::with(['booking', 'manipulation'])
            ->where('start', '>=', now())
            ->where('start', '<=', now()->addHours($firstReminderDelay * 2))
            ->get();

        foreach ($slots as $slot) {
            foreach ($slot->bookings as $booking) {
                $startingIn = now()->diffInHours($slot->start, absolute: true);
                $cancellationUrl = MagicLink::create(new CancelBookingAction($booking), now()->diffInSeconds($slot->start, true), 1)->url;
                if ($startingIn >= $firstReminderDelay && $startingIn < ($firstReminderDelay + 1)) {
                    Mail::to($booking->email)->send(new BookingReminder($booking, $cancellationUrl));
                }
                if ($startingIn >= $lastReminderDelay && $startingIn < ($lastReminderDelay + 1)) {
                    Mail::to($booking->email)->send(new BookingReminder($booking, $cancellationUrl));
                }
            }
        }
    }
}
