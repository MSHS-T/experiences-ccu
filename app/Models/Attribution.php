<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class Attribution extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'plateau_id',
        'manipulation_manager_id',
        'creator_id',
        'start_date',
        'end_date',
        'allowed_halfdays',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id'               => 'integer',
        'start_date'       => 'date',
        'end_date'         => 'date',
        'allowed_halfdays' => 'array',
    ];

    /**
     * The "booted" method of the model.
     */
    protected static function booted(): void
    {
        static::creating(function (Attribution $attribution) {
            $attribution->creator()->associate(
                App::runningInConsole()
                    ? User::role('administrator')->first()
                    : Auth::user()
            );
        });
    }

    public function plateau(): BelongsTo
    {
        return $this->belongsTo(Plateau::class, 'plateau_id', 'id');
    }

    public function manipulationManager(): BelongsTo
    {
        return $this->belongsTo(User::class, 'manipulation_manager_id', 'id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creator_id', 'id');
    }

    /**
     * Scope a query to only include attributions where end_date is in the future
     */
    public function scopeNotFinished(Builder $query): void
    {
        $query->where('end_date', '>=', now());
    }

    public function getAllowedHalfdaysDisplay(): array
    {
        $days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday'];
        return collect($this->allowed_halfdays)
            ->sort(function ($a, $b) use ($days) {
                $daysCmp = array_search(Str::before($a, '_'), $days) <=> array_search(Str::before($b, '_'), $days);
                if ($daysCmp != 0) {
                    return $daysCmp;
                }
                return $a <=> $b;
            })
            ->map(function ($hd) {
                [$day, $halfday] = explode('_', $hd);
                return __('attributes.' . $day) . ' ' . __('attributes.' . $halfday);
            })
            ->all();
    }

    public function getSimplifiedAllowedHalfdaysDisplay(): array
    {
        $days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday'];
        $halfdays = ['am', 'pm'];

        $result = collect();
        foreach ($days as $day) {
            $am = $day . '_am';
            $pm = $day . '_pm';
            if (in_array($am, $this->allowed_halfdays) && in_array($pm, $this->allowed_halfdays)) {
                $result->push(__('attributes.' . $day));
            } else {
                foreach ($halfdays as $halfday) {
                    if (in_array($$halfday, $this->allowed_halfdays)) {
                        $result->push(__('attributes.' . $day) . ' ' . __('attributes.' . $halfday));
                    }
                }
            }
        }
        return $result->all();
    }
}
