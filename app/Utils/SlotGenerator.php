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
    public static function estimateCount(?Plateau $plateau, ?string $startDate, ?string $endDate, ?array $availableHours, int|string|null $duration): ?int
    {
        if (blank($plateau)) return null;
        if (blank($startDate) || blank(self::parseDate($startDate))) return null;
        if (blank($endDate) || blank(self::parseDate($endDate)) || $endDate < $startDate) return null;
        if (blank($duration) || $duration <= 0) return null;
        if (blank($availableHours) || !self::validateAvailableHours($availableHours, $duration)) return null;

        return self::make($plateau, $startDate, $endDate, $availableHours, $duration)->count();
    }

    public static function validateAvailableHours(array $availableHours, int $duration): bool
    {
        foreach ($availableHours as $hours) {
            // Empty day : skip
            if (count(array_filter(array_values($hours))) == 0) continue;

            // Missing start or end hour : fail
            if (filled($hours['start_am']) && blank($hours['end_am'])) return false;
            if (blank($hours['start_am']) && filled($hours['end_am'])) return false;
            if (filled($hours['start_pm']) && blank($hours['end_pm'])) return false;
            if (blank($hours['start_pm']) && filled($hours['end_pm'])) return false;

            // End before/equal start : fail
            if ($hours['end_am'] <= $hours['start_am']) return false;
            if ($hours['end_pm'] <= $hours['start_pm']) return false;
        }
        return true;
    }


    public static function make(Plateau $plateau, string $startDate, string $endDate, array $availableHours, int $duration): Collection
    {
        $startDate = self::parseDate($startDate);
        $endDate = self::parseDate($endDate);

        $attributions = self::getAttributions($plateau, $startDate, $endDate);
        $otherSlots = self::getOtherSlots($plateau, $startDate, $endDate);

        $slots = collect();
        $cursorDate = $startDate->clone();
        while ($cursorDate <= $endDate) {
            $dow = Str::lower($cursorDate->format('l'));
            if (Arr::has($availableHours, $dow)) {
                $dowHours = $availableHours[$dow];

                foreach (['am', 'pm'] as $halfDay) {
                    // check attribution
                    if (!self::hasAttributionForHalfDay($attributions, $cursorDate, $halfDay)) continue;

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
            }
            $cursorDate->addDay()->startOfDay();
        }

        // ray($otherSlots->map(fn ($s) => $s['start']->format('Y-m-d') . ':' . $s['start']->format('H:i') . '-' . $s['end']->format('H:i')));
        // ray($slots->map(fn ($s) => $s['start']->format('Y-m-d') . ':' . $s['start']->format('H:i') . '-' . $s['end']->format('H:i')));

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

    public static function parseDate(string $date): ?Carbon
    {
        if (Carbon::hasFormat($date, 'Y-m-d H:i:s')) {
            return self::parseDate(Arr::first(explode(' ', $date)));
        } else if (Carbon::hasFormat($date, 'Y-m-d')) {
            return Carbon::createFromFormat('Y-m-d', $date);
        }
        return null;
    }

    public static function makeFromManipulation(Manipulation $m): Collection
    {
        return self::make($m->plateau, $m->start_date, $m->end_date, $m->available_hours, $m->duration);
    }

    public static function getOtherSlots(Plateau $plateau, Carbon $start_date, Carbon $end_date): Collection
    {
        return Slot::with('manipulation')
            ->whereHas('manipulation', fn (Builder $query) => $query->where('plateau_id', $plateau->id))
            ->where('start', '>=', $start_date->startOfDay())
            ->where('end', '<=', $end_date->endOfDay())
            ->get();
    }

    public static function hasSlotConflict(Collection $slots, Carbon $start, Carbon $end): bool
    {
        // TODO : compare performances with other collection methods (some, every) ?
        return $slots->filter(fn (Slot $s) => $s->start < $end && $s->end > $start)->isNotEmpty();
    }

    public static function getAttributions(Plateau $plateau, Carbon $start_date, Carbon $end_date): Collection
    {
        return Attribution::where('plateau_id', $plateau->id)
            ->where('manipulation_manager_id', Auth::id())
            ->where('start_date', '<=', $end_date)
            ->where('end_date', '>=', $start_date)
            ->get();
    }

    public static function hasAttributionForHalfDay(Collection $attributions, Carbon $date, string $halfDay): bool
    {
        $dow = Str::lower($date->format('l'));
        // TODO : compare performances with other collection methods (some, every) ?
        return $attributions->filter(
            fn (Attribution $attribution) => $attribution->start_date <= $date
                && $attribution->end_date >= $date
                && in_array($dow . '_' . $halfDay, $attribution->allowed_halfdays)
        )->isNotEmpty();
    }
}
