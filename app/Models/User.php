<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'project_id',
        'must_change_password',
        'job_title',
        'status',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'must_change_password' => 'boolean',
        ];
    }

    /**
     * Get the project that owns the user.
     */
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function projects(): BelongsToMany
    {
        return $this->belongsToMany(Project::class)->withTimestamps();
    }

    public function managedProject(): HasOne
    {
        return $this->hasOne(Project::class, 'manager_id');
    }

    /** Project this user may approve tickets for (manager_id only) — legacy single-project helper. */
    public function approverProjectId(): ?int
    {
        return $this->managedProject?->id;
    }

    /** All project ids this user may approve tickets for (every project where they are manager_id). */
    public function approverProjectIds()
    {
        return Project::where('manager_id', $this->id)->pluck('id');
    }

    public function isApproverForProject(int $projectId): bool
    {
        return $this->approverProjectIds()->contains($projectId);
    }

    public function memberProjectIds()
    {
        return $this->projects()
            ->pluck('projects.id')
            ->when($this->project_id, fn ($ids) => $ids->push($this->project_id))
            ->unique()
            ->filter()
            ->values();
    }

    public function syncPrimaryProjectMembership(?int $projectId): void
    {
        if ($projectId) {
            $this->projects()->syncWithoutDetaching([$projectId]);
        }
    }
}
