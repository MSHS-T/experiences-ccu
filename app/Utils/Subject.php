<?php

namespace App\Utils;

use App\Models\Booking;
use App\Models\BookingHistory;
use Illuminate\Database\Eloquent\Collection;

class Subject
{

    protected string $email;
    protected string $hashedEmail;
    protected Collection $bookings;
    protected ?BookingHistory $bookingHistory;

    public static function find(string $email): ?static
    {
        $allBookings = Booking::with(['slot', 'slot.manipulation'])
            ->where('email', $email)
            ->get();

        $hashedEmail = md5($email);

        // Get booking history
        $bookingHistory = BookingHistory::where('hashed_email', md5($email))->first();

        if ($allBookings->isNotEmpty() || filled($bookingHistory)) {
            return new static($email, $allBookings, $bookingHistory);
        }
        return null;
    }

    public function __construct(string $email, Collection $bookings, ?BookingHistory $bookingHistory)
    {
        $this->email = $email;
        $this->hashedEmail = md5($email);
        $this->bookings = $bookings;
        $this->bookingHistory = $bookingHistory;
    }

    public function history($withPercentage = false): array
    {
        $history = [
            'made'                => $this->bookingHistory ? $this->bookingHistory->booking_made : 0,
            'confirmed'           => $this->bookingHistory ? $this->bookingHistory->booking_confirmed : 0,
            'confirmed_honored'   => $this->bookingHistory ? $this->bookingHistory->booking_confirmed_honored : 0,
            'unconfirmed_honored' => $this->bookingHistory ? $this->bookingHistory->booking_unconfirmed_honored : 0,
            'blocked'             => $this->bookingHistory ? $this->bookingHistory->blocked : false,
        ];

        // List past bookings (not archived yet) and add them to booking history
        $this->bookings->each(function (Booking $booking) use (&$history) {
            $history['made']                += 1;
            $history['confirmed']           += $booking->confirmed ? 1 : 0;
            $history['confirmed_honored']   += ($booking->confirmed && $booking->honored) ? 1 : 0;
            $history['unconfirmed_honored'] += (!$booking->confirmed && $booking->honored) ? 1 : 0;
        });

        if ($withPercentage) {
            $history['confirmed_percentage']           = $history['made']                           === 0 ? 0
                : $history['confirmed'] / $history['made'] * 100;
            $history['confirmed_honored_percentage']   = $history['confirmed']                      === 0 ? 0
                : $history['confirmed_honored'] / $history['confirmed'] * 100;
            $history['unconfirmed_honored_percentage'] = ($history['made'] - $history['confirmed']) === 0 ? 0
                : $history['unconfirmed_honored'] / ($history['made'] - $history['confirmed']) * 100;
        }

        return $history;
    }

    public function isBlocked(): bool
    {
        return $this->bookingHistory ? $this->bookingHistory->blocked : false;
    }

    public function getUnhonoredCount(): int
    {
        $history = $this->history();
        return $history['made'] - $history['confirmed_honored'] - $history['unconfirmed_honored'];
    }

    public function futureBookings(): Collection
    {
        return $this->bookings->filter(fn (Booking $booking) => $booking->slot->start > now());
    }

    public function block(): void
    {
        if (!$this->bookingHistory) {
            $history = $this->history();
            $this->bookingHistory = BookingHistory::create([
                'hashed_email'                => $this->hashedEmail,
                'booking_made'                => $history['made'],
                'booking_confirmed'           => $history['confirmed'],
                'booking_confirmed_honored'   => $history['confirmed_honored'],
                'booking_unconfirmed_honored' => $history['unconfirmed_honored'],
            ]);
        }
        $this->bookingHistory->blocked = true;
        $this->bookingHistory->save();
    }

    public function unblock(): void
    {
        if (!$this->bookingHistory) {
            $history = $this->history();
            $this->bookingHistory = BookingHistory::create([
                'hashed_email'                => $this->hashedEmail,
                'booking_made'                => $history['made'],
                'booking_confirmed'           => $history['confirmed'],
                'booking_confirmed_honored'   => $history['confirmed_honored'],
                'booking_unconfirmed_honored' => $history['unconfirmed_honored'],
            ]);
        }
        $this->bookingHistory->blocked = false;
        $this->bookingHistory->save();
    }
}
