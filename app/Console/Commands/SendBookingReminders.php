<?php

namespace App\Console\Commands;

use App\Actions\CancelBookingAction;
use App\Mail\BookingReminder;
use App\Models\Slot;
use App\Settings\GeneralSettings;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
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
    protected $description = 'Send booking reminder emails at configured intervals before slot start time';

    /**
     * Execute the console command.
     *
     * Sends reminder emails to users with upcoming bookings at two configured intervals:
     * - First reminder: sent at the configured first reminder delay before the slot
     * - Last reminder: sent at the configured last reminder delay before the slot
     */
    public function handle()
    {
        // Load configurable reminder timing settings from the database
        $settings = app(GeneralSettings::class);
        $firstReminderDelay = $settings->email_first_reminder_delay;
        $lastReminderDelay = $settings->email_last_reminder_delay;

        // Log the current settings for debugging
        Log::info("Reminder settings - First: {$firstReminderDelay}h, Last: {$lastReminderDelay}h");

        // Calculate the maximum delay to determine query range
        $maxDelay = max($firstReminderDelay, $lastReminderDelay);

        // Query slots that start within a reasonable future timeframe
        // Add buffer of 2 hours to account for timing precision and command execution intervals
        // Eager load all necessary relationships to prevent N+1 queries and missing data
        $slots = Slot::with(['bookings', 'manipulation.users'])
            ->whereBetween('start', [now(), now()->addHours($maxDelay + 2)])
            ->get();

        Log::info("Found {$slots->count()} slots to check for reminders");

        $remindersSent = 0;

        foreach ($slots as $slot) {
            foreach ($slot->bookings as $booking) {
                // Calculate how many hours (as float) until the slot starts (always positive)
                $startingIn = now()->diffInMinutes($slot->start, absolute: true) / 60;

                Log::debug("Checking booking {$booking->id} - slot starts in {$startingIn} hours");

                // Generate a magic link that allows one-click booking cancellation
                // The link expires when the slot starts and can only be used once
                $cancellationUrl = MagicLink::create(
                    new CancelBookingAction($booking),
                    now()->diffInSeconds($slot->start, true), // TTL in seconds until slot start
                    1 // Maximum uses: 1
                )->url;

                // Send first reminder if we're within the first reminder window (tight 1-hour window to prevent duplicates)
                // Example: if firstReminderDelay is 24, send between 24-25 hours before
                // Using floor() to ensure we only trigger once per hour boundary
                $hoursUntilSlot = floor($startingIn);
                if ($hoursUntilSlot == $firstReminderDelay) {
                    try {
                        Log::info("Sending first reminder to {$booking->email} for booking {$booking->id} at date {$slot->start->format('Y-m-d H:i:s')} (starting in {$startingIn}h)");
                        Mail::to($booking->email)->send(new BookingReminder($booking, $cancellationUrl));
                        $remindersSent++;
                    } catch (\Exception $e) {
                        Log::error("Failed to send first reminder for booking {$booking->id}: " . $e->getMessage());
                    }
                }

                // Send last reminder if we're within the last reminder window (tight 1-hour window to prevent duplicates)
                // Example: if lastReminderDelay is 2, send between 2-3 hours before
                if ($hoursUntilSlot == $lastReminderDelay) {
                    try {
                        Log::info("Sending last reminder to {$booking->email} for booking {$booking->id} at date {$slot->start->format('Y-m-d H:i:s')} (starting in {$startingIn}h)");
                        Mail::to($booking->email)->send(new BookingReminder($booking, $cancellationUrl));
                        $remindersSent++;
                    } catch (\Exception $e) {
                        Log::error("Failed to send last reminder for booking {$booking->id}: " . $e->getMessage());
                    }
                }
            }
        }

        Log::info("Reminder command completed. Sent {$remindersSent} reminder emails.");
        $this->info("Sent {$remindersSent} reminder emails.");
    }
}
