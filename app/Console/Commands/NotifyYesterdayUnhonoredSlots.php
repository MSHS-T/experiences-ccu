<?php

namespace App\Console\Commands;

use App\Mail\BookingFirstMiss;
use App\Mail\BookingThirdMiss;
use App\Models\Slot;
use App\Utils\Subject;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class NotifyYesterdayUnhonoredSlots extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:notify-yesterday-unhonored-slots';

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
        $slots = Slot::with(['bookings', 'manipulation'])
            ->whereDate('start', now()->subDay()->format('Y-m-d'))
            ->get();

        foreach ($slots as $slot) {
            foreach ($slot->bookings as $booking) {
                if ($booking->honored === false) {
                    $subject = Subject::find($booking->email);
                    $unhonoredCount = $subject->getUnhonoredCount();
                    if ($unhonoredCount % 3 === 1) {
                        // Notify first miss (or first miss after unblock)
                        Mail::to($booking->email)->send(new BookingFirstMiss($booking));
                    } else if ($unhonoredCount % 3 === 0) {
                        // Blacklist after third miss (or third miss after unblock)
                        $subject->block();
                        Mail::to($booking->email)->send(new BookingThirdMiss($booking));
                    }
                }
            }
        }
    }
}
