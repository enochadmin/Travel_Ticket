<?php

namespace App\Models;

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
        'travel_date',
        'return_date',
        'purpose',
        'status',
        'pm_id',
        'hod_id',
        'remarks',
        'rejection_reason',
        'pm_approved_at',
    ];

    protected $casts = [
        'pm_approved_at' => 'datetime',
    ];

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
}
