<?php

namespace App\Models;

use App\Settings\GeneralSettings;
use App\Utils\SlotGenerator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Stringable;

/**
 * App\Models\Manipulation
 *
 * @property int $id
 * @property string $name
 * @property string $description
 * @property int $plateau_id
 * @property int $duration
 * @property int $max_booking_per_slot
 * @property Carbon $start_date
 * @property Carbon $end_date
 * @property array $requirements
 * @property bool $published
 * @property bool $archived
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read mixed $display_name
 * @property-read \App\Models\Plateau $plateau
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Slot> $slots
 * @property-read int|null $slots_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ManipulationStatistics> $statistics
 * @property-read int|null $statistics_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User> $users
 * @property-read int|null $users_count
 * @method static Builder|Manipulation active()
 * @method static \Database\Factories\ManipulationFactory factory($count = null, $state = [])
 * @method static Builder|Manipulation newModelQuery()
 * @method static Builder|Manipulation newQuery()
 * @method static Builder|Manipulation query()
 * @method static Builder|Manipulation visibleForParticipants()
 * @method static Builder|Manipulation whereArchived($value)
 * @method static Builder|Manipulation whereCreatedAt($value)
 * @method static Builder|Manipulation whereDescription($value)
 * @method static Builder|Manipulation whereDuration($value)
 * @method static Builder|Manipulation whereEndDate($value)
 * @method static Builder|Manipulation whereId($value)
 * @method static Builder|Manipulation whereMaxBookingPerSlot($value)
 * @method static Builder|Manipulation whereName($value)
 * @method static Builder|Manipulation wherePlateauId($value)
 * @method static Builder|Manipulation wherePublished($value)
 * @method static Builder|Manipulation whereRequirements($value)
 * @method static Builder|Manipulation whereStartDate($value)
 * @method static Builder|Manipulation whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Manipulation extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'plateau_id',
        'name',
        'description',
        'duration',
        'max_booking_per_slot',
        'start_date',
        'end_date',
        'requirements',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id'                   => 'integer',
        'duration'             => 'integer',
        'max_booking_per_slot' => 'integer',
        'start_date'           => 'date',
        'end_date'             => 'date',
        'requirements'         => 'array',
        'published'            => 'boolean',
        'archived'             => 'boolean',
    ];

    /**
     * The model's default values for attributes.
     *
     * @var array
     */
    protected $attributes = [
        'published' => false,
        'archived'  => false,
    ];

    public function plateau(): BelongsTo
    {
        return $this->belongsTo(Plateau::class);
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class);
    }

    public function slots(): HasMany
    {
        return $this->hasMany(Slot::class);
    }

    public function bookings(): HasManyThrough
    {
        return $this->hasManyThrough(Booking::class, Slot::class);
    }

    public function statistics(): HasMany
    {
        return $this->hasMany(ManipulationStatistics::class);
    }

    protected function displayName(): Attribute
    {
        return Attribute::make(
            get: fn(mixed $value, array $attributes) => $attributes['name'] . ' (' . $this->plateau->name . ')',
        );
    }

    public function togglePublished()
    {
        $this->published = !$this->published;
        $this->save();
    }

    public function archive()
    {
        if ($this->end_date->isFuture()) {
            return false;
        }
        DB::beginTransaction();

        $this->archived = true;
        $this->save();

        $this->load(['slots', 'slots.booking']);

        $currentMonth = null;
        $statistics = [];

        $slots = $this->slots->sortBy('start');
        $lastHalfDay = null;

        foreach ($this->slots as $slot) {
            $slotMonth = $slot->start->format('Y-m');
            if ($slotMonth !== $currentMonth) {
                $currentMonth = $slotMonth;
                $statistics[$slotMonth] = [
                    'month'                       => $slotMonth,
                    'half_day_count'              => 0,
                    'slot_count'                  => 0,
                    'booking_made'                => 0,
                    'booking_confirmed'           => 0,
                    'booking_confirmed_honored'   => 0,
                    'booking_unconfirmed_honored' => 0,
                ];
            }

            $currentHalfDay = $slot->start->format('Y-m-d') . '_' . ($slot->start->hour < 14 ? 'am' : 'pm');
            if ($currentHalfDay !== $lastHalfDay) {
                $statistics[$slotMonth]['half_day_count']++;
                $lastHalfDay = $currentHalfDay;
            }

            $statistics[$slotMonth]['slot_count']++;
            $slot->bookings->each(function (Booking $booking) use (&$statistics, $slotMonth) {
                $statistics[$slotMonth]['booking_made']++;
                if ($booking->confirmed) {
                    $statistics[$slotMonth]['booking_confirmed']++;
                    if ($booking->honored) {
                        $statistics[$slotMonth]['booking_confirmed_honored']++;
                    }
                } else {
                    if ($booking->honored) {
                        $statistics[$slotMonth]['booking_unconfirmed_honored']++;
                    }
                }
            });
        }
        $this->statistics()->createMany(array_values($statistics));
        $this->slots->each(fn(Slot $slot) => $slot->delete());

        DB::commit();
    }

    public function createOrUpdateSlots()
    {
        $this->loadMissing('slots');
        $newSlots = SlotGenerator::makeFromManipulation($this);

        // Convert existing slots to a comparable format
        $existingSlotKeys = $this->slots->mapWithKeys(function (Slot $slot) {
            $key = $slot->start->format('Y-m-d H:i:s') . ';' . $slot->end->format('Y-m-d H:i:s');
            return [$key => $slot];
        });

        // Convert new slots to a comparable format
        $newSlotKeys = $newSlots->mapWithKeys(function ($slot) {
            $key = $slot['start']->format('Y-m-d H:i:s') . ';' . $slot['end']->format('Y-m-d H:i:s');
            return [$key => $slot];
        });

        // Find slots to delete (exist in current but not in new)
        $slotsToDelete = $existingSlotKeys->keys()->diff($newSlotKeys->keys());

        // Find slots to create (exist in new but not in current)
        $slotsToCreate = $newSlotKeys->filter(function ($slot, $key) use ($existingSlotKeys) {
            return !$existingSlotKeys->has($key);
        });

        // Delete unnecessary slots
        $this->slots->whereIn('id', $slotsToDelete->map(fn($key) => $existingSlotKeys[$key]->id))->each->delete();

        // Create new slots
        $this->slots()->createMany($slotsToCreate);
    }

    /**
     * Scope a query to only include manipulations visible for the public.
     */
    public function scopeVisibleForParticipants(Builder $query): void
    {
        $booking_opening_delay = app(GeneralSettings::class)->booking_opening_delay;
        $query->where('published', true)
            ->where('archived', false)
            ->where('start_date', '<=', Carbon::today()->addDays($booking_opening_delay))
            ->where('end_date', '>', Carbon::today())
            ->orderBy('end_date', 'asc');
    }

    /**
     * Scope a query to only include active manipulations.
     */
    public function scopeActive(Builder $query): void
    {
        $booking_opening_delay = app(GeneralSettings::class)->booking_opening_delay;
        $query->where('published', true)
            ->where('archived', false)
            ->where('start_date', '<=', Carbon::today()->addDays($booking_opening_delay))
            ->where('end_date', '>', Carbon::today());
    }
}
