<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;

class Manipulation extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'description',
        'plateau_id',
        'duration',
        'target_slots',
        'start_date',
        'location',
        'requirements',
        'available_hours'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'available_hours' => 'array',
        'requirements'    => 'array',
    ];

    /**
     * The relationships that should always be included
     */
    protected $with = ['managers'];

    public function plateau()
    {
        return $this->belongsTo('App\Plateau', 'plateau_id');
    }

    public function managers()
    {
        return $this->belongsToMany('App\User');
    }

    public function slots()
    {
        return $this->hasMany('App\Slot');
    }

    public function availableSlots()
    {
        return $this->slots()->doesntHave('booking');
    }

    public function statistics()
    {
        return $this->hasOne('App\ManipulationStatistics');
    }

    /**
     * Scope a query to only include manipulations with available slots.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWhereHasAvailableSlots($query)
    {
        return $query->whereHas('availableSlots');
    }

    public function generateSlots($fromDate = false, $toDate = false)
    {
        // Existing slots
        $existingSlots = $this->slots()->orderBy('start')->get();

        // If no date was given, we generate until quota is met
        if ($fromDate === false) {
            // If no slots exist we start at the manipulation start date
            if ($this->slots->isEmpty()) {
                $fromDate = $this->start_date;
            }
            // If slots already exist, we start the day after the last slot
            else {
                $fromDate = $existingSlots->last()->start->addDay();
            }
        }

        // Generate all slots if manipulation doesn't have any
        $hours = $this->available_hours;
        $duration = $this->duration;

        // Compute target slot count based on manipulation data and overbooking setting
        // Overbooking setting will be a numeric percentage string => we need to cast it as int and then divide by 100 to get a multiplier
        $overbooking_multiplier = (intval(Setting::get('manipulation_overbooking')) / 100);
        // Round the number up
        $target_slots = ceil($this->target_slots * $overbooking_multiplier);

        $current_date = new Carbon($fromDate);
        $current_date->startOfDay();
        $slots = [];
        do {
            $day = $current_date->shortEnglishDayOfWeek;
            if ($hours[$day]['enabled'] === true) {
                foreach (['am', 'pm'] as $ampm) {
                    if ($hours[$day][$ampm] === true) {
                        list($start_h, $start_m) = array_map('intval', explode(':', $hours[$day]['start_' . $ampm]));
                        list($end_h, $end_m) = array_map('intval', explode(':', $hours[$day]['end_' . $ampm]));
                        $current_date->setTime($start_h, $start_m, 0);
                        $limit = $current_date->copy()->setTime($end_h, $end_m, 0);
                        while ($current_date->diffInMinutes($limit) >= $duration) {
                            $next_date = $current_date->copy()->addMinutes($duration);
                            $slots[] = ['start' => $current_date, 'end' => $next_date];
                            $current_date = $next_date->copy();
                        }
                    }
                }
            }
            $current_date->startOfDay()->addDay();
            if ($toDate !== false && $current_date->greaterThan(Carbon::create($toDate)->endOfDay())) {
                break;
            }
        } while ((count($slots) + count($existingSlots)) < $target_slots);

        // Filter slots against existing ones (if any) to avoid duplicates
        $slots = array_filter($slots, function ($s) use ($existingSlots) {
            return $existingSlots->isEmpty() || $existingSlots->every(function ($slot) use ($s) {
                return $s['start']->greaterThanOrEqualTo($slot->end)
                    || $s['end']->lessThanOrEqualTo($slot->start);
            });
        });

        $this->slots()->createMany($slots);
    }

    /**
     * Delete the model from the database.
     *
     * @return bool|null
     *
     * @throws \Exception
     * @throws \UnexpectedValueException
     */
    public function delete()
    {
        // Check for booked slots in the future
        // If it has any, throw an exception
        $hasFutureBookedSlots = $this->slots->contains(function ($slot) {
            return $slot->start > Carbon::now() && $slot->booking !== null;
        });
        if ($hasFutureBookedSlots) {
            throw new \UnexpectedValueException("Manipulation cannot be deleted because it has booked slots in the future.");
        }

        // Create stats model
        $stats = new ManipulationStatistics();

        // Arrays to keep track of all bookings to delete them later without looping again
        $bookingsToDelete = [];

        // Loop on slots, and feed data into ManipulationStatistics and BookingHistory
        foreach ($this->slots as $slot) {
            $stats->slot_count++;
            if (null !== ($booking = $slot->booking)) {
                $bookingHistory = BookingHistory::findByEmailOrCreate($booking->email);
                $bookingHistory->booking_made++;
                $stats->booking_made++;

                if ($booking->confirmed) {
                    $bookingHistory->booking_confirmed++;
                    $stats->booking_confirmed++;

                    if ($booking->honored) {
                        $bookingHistory->booking_confirmed_honored++;
                        $stats->booking_confirmed_honored++;
                    }
                } else {
                    if ($booking->honored) {
                        $bookingHistory->booking_unconfirmed_honored++;
                        $stats->booking_unconfirmed_honored++;
                    }
                }

                $bookingHistory->save();
                $bookingsToDelete[] = $booking;
            }
        }

        $this->statistics()->save($stats);

        // Delete bookings then slots
        array_map(function ($booking) {
            $booking->delete();
        }, $bookingsToDelete);
        $this->slots->each(function ($slot) {
            $slot->delete();
        });
        // Delete (soft) the manipulation
        parent::delete();
    }
}
