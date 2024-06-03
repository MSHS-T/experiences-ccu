<?php

namespace App\Utils;

use App\Models\ManipulationStatistics;
use App\Models\Slot;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class Statistics
{
    public static function getMonths(): array
    {
        $min = min([
            ManipulationStatistics::min('month'),
            (new Carbon(Slot::min('start')))->format('Y-m'),
        ]);
        $max = min([
            max([
                ManipulationStatistics::max('month'),
                (new Carbon(Slot::max('start')))->format('Y-m'),
            ]),
            now()->format('Y-m')
        ]);

        // Generate all months (Y-m format) between min and max
        $months = [$min];
        while (last($months) < $max) {
            $months[] = (new Carbon(last($months)))->addMonth()->format('Y-m');
        }

        return $months;
    }

    public static function getYears(): array
    {
        $min = min([
            (new Carbon(ManipulationStatistics::min('month')))->format('Y'),
            (new Carbon(Slot::min('start')))->format('Y'),
        ]);
        $max = min([
            max([
                (new Carbon(ManipulationStatistics::max('month')))->format('Y'),
                (new Carbon(Slot::max('start')))->format('Y'),
            ]),
            now()->format('Y')
        ]);

        // Generate all years (Y format) between min and max
        $years = [$min];
        while (last($years) < $max) {
            $years[] = (new Carbon(last($years)))->addYear()->format('Y-m');
        }

        return $years;
    }

    public static function getMonthlyStatistics(string $month = null): Collection
    {
        $history = ManipulationStatistics::with(['manipulation', 'manipulation.plateau'])
            ->when(filled($month), fn (Builder $query) => $query->where('month', $month))
            ->get();

        $slots = Slot::with(['manipulation', 'manipulation.plateau', 'booking'])
            ->when(filled($month), function (Builder $query) use ($month) {
                [$y, $m] = array_map('intval', explode('-', $month));
                return $query->whereYear('start', $y)
                    ->whereMonth('start', $m);
            })
            ->get();

        return self::buildStatistics($history, $slots);
    }

    public static function getYearlyStatistics(string $year = null): Collection
    {
        $history = ManipulationStatistics::with(['manipulation', 'manipulation.plateau'])
            ->when(filled($year), fn (Builder $query) => $query->where('month', 'like', $year . '%'))
            ->get();

        $slots = Slot::with(['manipulation', 'manipulation.plateau', 'booking'])
            ->when(filled($year), fn (Builder $query) => $query->whereYear('start', $year))
            ->get();

        return self::buildStatistics($history, $slots);
    }

    public static function buildStatistics(Collection $history, Collection $slots): Collection
    {
        $stats = $history->map(function (ManipulationStatistics $st) {
            return [
                'plateau_id'        => $st->manipulation->plateau_id,
                'plateau'           => $st->manipulation->plateau,
                'slot_count'        => $st->slot_count,
                'hour_count'        => $st->slot_count * $st->manipulation->duration / 60,
                'booking_made'      => $st->booking_made,
                'booking_confirmed' => $st->booking_confirmed,
                'booking_confirmed_honored' => $st->booking_confirmed_honored,
                'booking_unconfirmed_honored' => $st->booking_unconfirmed_honored,
            ];
        })->reduce(function (array $carry, array $item) {
            if (!array_key_exists($item['plateau_id'], $carry)) {
                $carry[$item['plateau_id']] = $item;
            } else {
                $carry[$item['plateau_id']]['slot_count']                  += $item['slot_count'];
                $carry[$item['plateau_id']]['hour_count']                  += $item['hour_count'];
                $carry[$item['plateau_id']]['booking_made']                += $item['booking_made'];
                $carry[$item['plateau_id']]['booking_confirmed']           += $item['booking_confirmed'];
                $carry[$item['plateau_id']]['booking_confirmed_honored']   += $item['booking_confirmed_honored'];
                $carry[$item['plateau_id']]['booking_unconfirmed_honored'] += $item['booking_unconfirmed_honored'];
            }
            return $carry;
        }, []);

        foreach ($slots as $slot) {
            $plateau = $slot->manipulation->plateau_id;
            /** @var Slot $slot */
            if (!array_key_exists($plateau, $stats)) {
                $stats[$plateau] = [
                    'plateau_id'                  => $plateau,
                    'plateau'                     => $slot->manipulation->plateau,
                    'slot_count'                  => 0,
                    'hour_count'                  => 0,
                    'booking_made'                => 0,
                    'booking_confirmed'           => 0,
                    'booking_confirmed_honored'   => 0,
                    'booking_unconfirmed_honored' => 0,
                ];
            }
            $stats[$plateau]['slot_count']                  += 1;
            $stats[$plateau]['hour_count']                  += $slot->manipulation->duration / 60;
            $stats[$plateau]['booking_made']                += filled($slot->booking) ? 1 : 0;
            $stats[$plateau]['booking_confirmed']           += (filled($slot->booking) && $slot->booking->confirmed) ? 1 : 0;
            $stats[$plateau]['booking_confirmed_honored']   += (filled($slot->booking) && $slot->booking->confirmed && $slot->booking->honored) ? 1 : 0;
            $stats[$plateau]['booking_unconfirmed_honored'] += (filled($slot->booking) && !$slot->booking->confirmed && $slot->booking->honored) ? 1 : 0;
        }

        return collect($stats)->map(fn ($item) => (object) [
            'plateau'           => $item['plateau'],
            'slot_count'        => $item['slot_count'],
            'hour_count'        => $item['hour_count'],
            'booking_rate'      => $item['booking_made'] / $item['slot_count'] * 100,
            'confirmation_rate' => $item['booking_confirmed'] / $item['slot_count'] * 100,
            'presence_rate'     => ($item['booking_confirmed_honored'] + $item['booking_unconfirmed_honored']) / $item['booking_made'] * 100,
        ])
            ->sortBy(fn ($item) => $item->plateau->name);
    }
}
