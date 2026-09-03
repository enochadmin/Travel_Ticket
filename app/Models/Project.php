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

    /**
     * Whether the assigned manager is a Commercial Director — i.e. the project
     * has no Project Manager, so requests skip the PM stage and go straight
     * to the Commercial Director for approval.
     */
    public function managerIsCommercialDirector(): bool
    {
        $manager = $this->resolveManager();

        return $manager !== null && $manager->hasRole('commercial-director');
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
            // Source of truth: the discipline field set to "Head-Office" / "Head Office"
            // (case and separator insensitive), e.g. for head-office departments.
            $q->where(function ($discipline) {
                foreach (['%head-office%', '%head office%', '%headoffice%'] as $pattern) {
                    $discipline->orWhereRaw('LOWER(discipline) LIKE ?', [$pattern]);
                }
            });

            // Legacy: projects created before the discipline flag existed were
            // categorised by matching "head office" in their name/location columns.
            foreach (['name', 'region', 'location', 'project_code'] as $column) {
                $q->orWhereRaw("LOWER({$column}) LIKE ?", ['%head office%']);
            }
        });
    }

    public function isHeadOffice(): bool
    {
        $discipline = $this->discipline;
        if ($discipline && preg_match('/head[\s-]?office/i', (string) $discipline)) {
            return true;
        }

        // Legacy fallback for projects created before the discipline flag existed.
        foreach (['name', 'region', 'location', 'project_code'] as $field) {
            $value = $this->{$field};
            if ($value && str_contains(strtolower((string) $value), 'head office')) {
                return true;
            }
        }

        return false;
    }
}
