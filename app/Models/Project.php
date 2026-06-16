<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Project extends Model
{
    protected $fillable = [
        'name',
        'project_code',
        'description',
        'location',
        'region',
        'discipline',
        'manager_id',
        'start_date',
        'end_date',
        'status',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    /** The assigned Project Manager */
    public function manager(): BelongsTo
    {
        return $this->belongsTo(User::class, 'manager_id');
    }

    public function resolveManager(): ?User
    {
        if (! $this->manager_id) {
            return null;
        }

        return $this->relationLoaded('manager') ? $this->manager : $this->manager()->first();
    }

    public function hasManager(): bool
    {
        return (bool) $this->manager_id;
    }

    /** Staff assigned to this project */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function members(): BelongsToMany
    {
        return $this->belongsToMany(User::class)->withTimestamps();
    }

    public function travelRequests(): HasMany
    {
        return $this->hasMany(TravelRequest::class);
    }

    public function scopeHeadOffice(Builder $query): Builder
    {
        return $query->where(function ($q) {
            foreach (['name', 'region', 'location', 'project_code'] as $column) {
                $q->orWhereRaw("LOWER({$column}) LIKE ?", ['%head office%']);
            }
        });
    }

    public function isHeadOffice(): bool
    {
        foreach (['name', 'region', 'location', 'project_code'] as $field) {
            $value = $this->{$field};
            if ($value && str_contains(strtolower((string) $value), 'head office')) {
                return true;
            }
        }

        return false;
    }
}
