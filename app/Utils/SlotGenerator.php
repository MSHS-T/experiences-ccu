<?php

namespace App\Utils;

use App\Models\Attribution;
use App\Models\Manipulation;
use App\Models\Plateau;
use App\Models\Slot;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class SlotGenerator
{
    public static function estimateCount(array $userIds, ?Plateau $plateau, string|Carbon|null $startDate, string|Carbon|null $endDate, int|string|null $duration, int|null $manipulationId = null): ?int
    {
        if (blank($userIds)) return null;
        if (blank($plateau)) return null;
        if (blank($startDate) || blank(self::parseDate($startDate))) return null;
        if (blank($endDate) || blank(self::parseDate($endDate)) || $endDate < $startDate) return null;
        if (blank($duration) || $duration <= 0) return null;

        $value = self::make($userIds, $plateau, $startDate, $endDate, $duration, $manipulationId)->count();
        return $value;
    }

    public static function make(array $userIds, Plateau $plateau, string|Carbon $startDate, string|Carbon $endDate, int $duration, int|null $manipulationId = null): Collection
    {
        $startDate = self::parseDate($startDate);
        $endDate = self::parseDate($endDate);

        $attributions = self::getAttributions($userIds, $plateau, $startDate, $endDate);
        $otherSlots = self::getOtherSlots($plateau, $startDate, $endDate);
        if ($manipulationId) {
            $otherSlots = $otherSlots->filter(fn(Slot $slot) => $slot->manipulation->id !== $manipulationId);
        }

        $slots = collect();
        $cursorDate = $startDate->clone();
        while ($cursorDate <= $endDate) {
            $dow = $cursorDate->format('l');
            if (in_array($dow, config('collabccu.default_days'))) {
                foreach (['am', 'pm'] as $halfDay) {
                    // check attribution
                    if (!self::hasAttributionForHalfDay($attributions, $cursorDate, $halfDay)) continue;

                    $startHalfDay = self::parseTime(config('collabccu.default_hours.start_' . $halfDay));
                    $endHalfDay   = self::parseTime(config('collabccu.default_hours.end_' . $halfDay));
                    if (is_null($startHalfDay) || is_null($endHalfDay)) {
                        continue;
                    }

                    $startHalfDay = $cursorDate->clone()->setTime(...explode(':', $startHalfDay));
                    $endHalfDay = $cursorDate->clone()->setTime(...explode(':', $endHalfDay));

                    $cursorHalfDay = $startHalfDay->clone();

                    do {
                        $start = $cursorHalfDay->clone();
                        $end = $cursorHalfDay->addMinutes($duration)->clone();
                        if ($end <= $endHalfDay) {
                            // check for collisions
                            if (self::hasSlotConflict($otherSlots, $start, $end)) {
                                continue;
                            }
                            $slots->push([
                                'start' => $start,
                                'end'   => $end
                            ]);
                        }
                    } while ($cursorHalfDay < $endHalfDay);
                }
            }
            $cursorDate->addDay()->startOfDay();
        }

        return $slots;
    }

    public static function availableZones(Manipulation $manipulation, string|Carbon $startDate, string|Carbon $endDate): Collection
    {
        $startDate    = self::parseDate($startDate);
        $endDate      = self::parseDate($endDate);
        $attributions = self::getAttributions($manipulation->users->pluck('id')->all(), $manipulation->plateau, $startDate, $endDate);
        // Do not include slots from current manipulation as potential conflicts
        $otherSlots   = self::getOtherSlots($manipulation->plateau, $startDate, $endDate)
            ->filter(fn(Slot $slot) => $slot->manipulation->id !== $manipulation->id);

        $slots = collect();
        $cursorDate = $startDate->clone();
        while ($cursorDate <= $endDate) {
            $dow = Str::lower($cursorDate->format('l'));
            foreach (['am', 'pm'] as $halfDay) {
                // check attribution
                if (!self::hasAttributionForHalfDay($attributions, $cursorDate, $halfDay)) continue;

                $startHalfDay = self::parseTime(config('collabccu.default_hours.start_' . $halfDay));
                $endHalfDay   = self::parseTime(config('collabccu.default_hours.end_' . $halfDay));

                if (is_null($startHalfDay) || is_null($endHalfDay)) {
                    continue;
                }

                $startHalfDay  = $cursorDate->clone()->setTime(...explode(':', $startHalfDay));
                $endHalfDay    = $cursorDate->clone()->setTime(...explode(':', $endHalfDay));
                $cursorHalfDay = $startHalfDay->clone();

                do {
                    $start = $cursorHalfDay->clone();
                    $end = $cursorHalfDay->addMinutes($manipulation->duration)->clone();
                    if ($end <= $endHalfDay) {
                        // check for collisions
                        if (self::hasSlotConflict($otherSlots, $start, $end)) {
                            continue;
                        }
                        $slots->push([
                            'start' => $start->format('Y-m-d H:i:s'),
                            'end'   => $end->format('Y-m-d H:i:s')
                        ]);
                    } else if ($end > $endHalfDay && $start < $endHalfDay) {
                        if ($slots->last()['end'] === $start->format('Y-m-d H:i:s'))
                            $slots->push([
                                'start' => $start->format('Y-m-d H:i:s'),
                                'end'   => $endHalfDay->format('Y-m-d H:i:s')
                            ]);
                    }
                } while ($cursorHalfDay < $endHalfDay);
            }
            $cursorDate->addDay()->startOfDay();
        }

        return $slots->reduce(
            function (Collection $carry, array $item) {
                if ($carry->isEmpty()) {
                    $carry->push($item);
                } else if ($carry->last()['end'] === $item['start']) {
                    $last = $carry->pop();
                    $carry->push([
                        'start' => $last['start'],
                        'end'   => $item['end']
                    ]);
                } else {
                    $carry->push($item);
                }
                return $carry;
            },
            collect([])
        );
    }

    public static function makeFromManipulation(Manipulation $m): Collection
    {
        return self::make(
            $m->users->pluck('id')->all(),
            $m->plateau,
            $m->start_date,
            $m->end_date,
            $m->duration,
            $m->id
        );
    }

    public static function makeFromManipulationAndDateTimes(Manipulation $m, string|Carbon $startDateTime, string|Carbon $endDateTime): Collection
    {
        $startDateTime = self::parseDateTime($startDateTime);
        $endDateTime = self::parseDateTime($endDateTime);
        $availableZones = self::availableZones($m, $startDateTime, $endDateTime);

        $slots = collect();

        // Iterate through available time frames
        foreach ($availableZones as $zone) {
            $zoneStart = Carbon::createFromFormat('Y-m-d H:i:s', $zone['start']);
            $zoneEnd = Carbon::createFromFormat('Y-m-d H:i:s', $zone['end']);

            // Check if the zone is within the overall start and end datetime
            if ($zoneStart->lessThanOrEqualTo($endDateTime) && $zoneEnd->greaterThanOrEqualTo($startDateTime)) {
                $currentTime = $startDateTime->greaterThan($zoneStart) ? $startDateTime : $zoneStart;

                // Add events within the available time frame
                while ($currentTime->addMinutes($m->duration)->lessThanOrEqualTo($zoneEnd) && $currentTime->lessThanOrEqualTo($endDateTime)) {
                    $slots->push([
                        'start' => $currentTime->copy()->subMinutes($m->duration)->format('Y-m-d H:i'),
                        'end' => $currentTime->format('Y-m-d H:i')
                    ]);
                }
            }
        }

        return $slots;
    }

    public static function getOtherSlots(Plateau $plateau, Carbon $start_date, Carbon $end_date): Collection
    {
        return Slot::with('manipulation')

            ->whereHas('manipulation', fn(Builder $query) => $query->where('plateau_id', $plateau->id))
            ->where('start', '>=', $start_date->startOfDay())
            ->where('end', '<=', $end_date->endOfDay())
            ->get();
    }

    public static function hasSlotConflict(Collection $slots, Carbon $start, Carbon $end): bool
    {
        return $slots->filter(fn(Slot $s) => $s->start < $end && $s->end > $start)->isNotEmpty();
    }

    public static function getAttributions(array $userIds, Plateau $plateau, Carbon $start_date, Carbon $end_date): Collection
    {
        return Attribution::where('plateau_id', $plateau->id)
            ->whereIn('manipulation_manager_id', $userIds)
            ->where('start_date', '<=', $end_date)
            ->where('end_date', '>=', $start_date)
            ->get();
    }

    public static function hasAttributionForHalfDay(Collection $attributions, Carbon $date, string $halfDay): bool
    {
        $dow = Str::lower($date->format('l'));
        return $attributions->filter(
            fn(Attribution $attribution) => $attribution->start_date <= $date
                && $attribution->end_date >= $date
                && in_array($dow . '_' . $halfDay, $attribution->allowed_halfdays)
        )->isNotEmpty();
    }

    public static function parseTime(string $time): ?string
    {
        if (Carbon::hasFormat($time, 'Y-m-d H:i:s')) {
            return Arr::last(explode(' ', $time));
        } else if (Carbon::hasFormat($time, 'H:i')) {
            return $time . ':00';
        }
        return null;
    }

    public static function parseDate(string|Carbon $date): ?Carbon
    {
        if ($date instanceof Carbon) {
            return $date->clone()->startOfDay();
        } else if (Carbon::hasFormat($date, 'Y-m-d H:i:s')) {
            return self::parseDate(Arr::first(explode(' ', $date)));
        } else if (Carbon::hasFormat($date, 'Y-m-d')) {
            return Carbon::createFromFormat('Y-m-d', $date);
        }
        return null;
    }

    public static function parseDateTime(string|Carbon $date): ?Carbon
    {
        if ($date instanceof Carbon) {
            return $date;
        } else if (Carbon::hasFormat($date, 'Y-m-d H:i:s')) {
            return Carbon::createFromFormat('Y-m-d H:i:s', $date);
        }
        return null;
    }
}
