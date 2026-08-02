<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TravelRequest extends Model
{
    protected $fillable = [
        'user_id',
        'project_id',
        'destination',
        'origin',
        'passenger_count',
        'additional_passengers',
        'flight_type',
        'travel_date',
        'return_date',
        'purpose',
        'status',
        'pm_id',
        'hod_id',
        'remarks',
        'rejection_reason',
        'pm_approved_at',
        'hod_approved_at',
        'archived_at',
        'archived_by',
    ];

    protected $casts = [
        'additional_passengers' => 'array',
        'pm_approved_at' => 'datetime',
        'hod_approved_at' => 'datetime',
        'archived_at' => 'datetime',
    ];

    /**
     * Full names of passengers other than the requester.
     *
     * @return array<int, string>
     */
    public function additionalPassengerNames(): array
    {
        return is_array($this->additional_passengers)
            ? array_values(array_filter($this->additional_passengers))
            : [];
    }

    /**
     * Requester plus any additional passengers (for display).
     *
     * @return array<int, string>
     */
    public function allPassengerNames(): array
    {
        $names = array_filter([$this->user?->name]);

        return array_merge($names, $this->additionalPassengerNames());
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function pm(): BelongsTo
    {
        return $this->belongsTo(User::class, 'pm_id');
    }

    public function hod(): BelongsTo
    {
        return $this->belongsTo(User::class, 'hod_id');
    }

    public function archivedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'archived_by');
    }

    public function scopeForHeadOffice(Builder $query): Builder
    {
        return $query->whereHas('project', fn (Builder $q) => $q->headOffice());
    }
}
