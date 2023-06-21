<?php

namespace App\Utils;

use App\Models\Manipulation;
use App\Models\Slot;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class SlotGenerator
{
    public static function estimateCount(?string $startDate, ?string $endDate, ?array $availableHours, ?int $duration): ?int
    {
        if (blank($startDate)) return null;
        if (blank($endDate) || $endDate < $startDate) return null;
        if (blank($duration) || $duration <= 0) return null;
        if (blank($availableHours) || !self::validateAvailableHours($availableHours, $duration)) return null;

        return self::make($startDate, $endDate, $availableHours, $duration)->count();
    }

    public static function validateAvailableHours(array $availableHours, int $duration): bool
    {
        foreach ($availableHours as $hours) {
            if (count(array_filter(array_values($hours))) == 0) return false;
            if (filled($hours['start_am']) && blank($hours['end_am'])) return false;
            if ($hours['end_am'] <= $hours['start_am']) return false;

            if (filled($hours['start_pm']) && blank($hours['end_pm'])) return false;
            if ($hours['end_pm'] <= $hours['start_pm']) return false;
        }
        return true;
    }


    public static function make(string $startDate, string $endDate, array $availableHours, int $duration): Collection
    {
        $slots = collect();
        $startDate = Carbon::createFromFormat('Y-m-d H:i:s', $startDate);
        $endDate = Carbon::createFromFormat('Y-m-d H:i:s', $endDate);
        $cursorDate = $startDate->clone();
        while ($cursorDate <= $endDate) {
            $dow = Str::lower($cursorDate->format('l'));
            if (Arr::has($availableHours, $dow)) {
                $dowHours = $availableHours[$dow];

                foreach (['am', 'pm'] as $halfDay) {
                    if (filled($dowHours['start_' . $halfDay]) && filled($dowHours['end_' . $halfDay])) {
                        $startHalfDay = self::parseTime($dowHours['start_' . $halfDay]);
                        $endHalfDay   = self::parseTime($dowHours['end_' . $halfDay]);
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
                                $slots->push([
                                    'start' => $start,
                                    'end'   => $end
                                ]);
                            }
                        } while ($cursorHalfDay < $endHalfDay);
                    }
                }
            }
            $cursorDate->addDay()->startOfDay();
        }

        return $slots;
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

    public static function makeFromManipulation(Manipulation $m): Collection
    {
        return self::make($m->start_date, $m->end_date, $m->available_hours, $m->duration);
    }
}
