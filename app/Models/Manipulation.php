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
 * @property \Illuminate\Support\Carbon $start_date
 * @property \Illuminate\Support\Carbon $end_date
 * @property array $available_hours
 * @property array $requirements
 * @property bool $published
 * @property bool $archived
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Plateau $plateau
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Slot> $slots
 * @property-read int|null $slots_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User> $users
 * @property-read int|null $users_count
 * @property-read Illuminate\Database\Eloquent\Collection<int, \App\Models\ManipulationStatistics>|null $statistics
 * @method static \Database\Factories\ManipulationFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|Manipulation newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Manipulation newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Manipulation query()
 * @method static \Illuminate\Database\Eloquent\Builder|Manipulation whereAvailableHours($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Manipulation whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Manipulation whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Manipulation whereDuration($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Manipulation whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Manipulation whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Manipulation wherePlateauId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Manipulation whereRequirements($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Manipulation whereStartDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Manipulation whereEndDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Manipulation whereTargetSlots($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Manipulation whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Manipulation wherePublished($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Manipulation whereArchived($value)
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
        'start_date',
        'end_date',
        'available_hours',
        'requirements',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id'              => 'integer',
        'duration'        => 'integer',
        'start_date'      => 'date',
        'end_date'        => 'date',
        'available_hours' => 'array',
        'requirements'    => 'array',
        'published'       => 'boolean',
        'archived'        => 'boolean',
    ];

    /**
     * The model's default values for attributes.
     *
     * @var array
     */
    protected $attributes = [
        'published' => false,
        'archived' => false,
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

    public function statistics(): HasMany
    {
        return $this->hasMany(ManipulationStatistics::class);
    }

    protected function displayName(): Attribute
    {
        return Attribute::make(
            get: fn (mixed $value, array $attributes) => $attributes['name'] . ' (' . $this->plateau->name . ')',
        );
    }

    public function getAvailableHoursDisplay(): array
    {
        $days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday'];

        return collect($this->available_hours)->map(function ($hours, $day) {
            if (collect($hours)->filter()->isEmpty()) {
                return null;
            }
            return __('attributes.' . $day)
                . ' : '
                . collect([
                    collect([$hours['start_am'], $hours['end_am']])->filter()->join('-'),
                    collect([$hours['start_pm'], $hours['end_pm']])->filter()->join('-')
                ])
                ->filter(fn ($s) => strlen($s) > 0)
                ->join(' &amp; ');
        })->filter()
            ->sortKeysUsing(fn ($a, $b) => array_search($a, $days) <=> array_search($b, $days))
            ->all();
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
        foreach ($this->slots as $slot) {
            $slotMonth = $slot->start->format('Y-m');
            if ($slotMonth !== $currentMonth) {
                $currentMonth = $slotMonth;
                $statistics[$slotMonth] = [
                    'month'                       => $slotMonth,
                    'slot_count'                  => 0,
                    'booking_made'                => 0,
                    'booking_confirmed'           => 0,
                    'booking_confirmed_honored'   => 0,
                    'booking_unconfirmed_honored' => 0,
                ];
            }
            $statistics[$slotMonth]['slot_count']++;
            if ($slot->booking !== null) {
                $statistics[$slotMonth]['booking_made']++;
                if ($slot->booking->confirmed) {
                    $statistics[$slotMonth]['booking_confirmed']++;
                    if ($slot->booking->honored) {
                        $statistics[$slotMonth]['booking_confirmed_honored']++;
                    }
                } else {
                    if ($slot->booking->honored) {
                        $statistics[$slotMonth]['booking_unconfirmed_honored']++;
                    }
                }
            }
        }
        $this->statistics()->createMany(array_values($statistics));
        $this->slots->each(fn (Slot $slot) => $slot->delete());

        DB::commit();
    }

    public function createOrUpdateSlots()
    {
        $this->slots()->createMany(SlotGenerator::makeFromManipulation($this));
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
     * Scope a query to only include manipulations visible for the public.
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
