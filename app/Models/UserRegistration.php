<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserRegistration extends Model
{
    protected $fillable = [
        'name',
        'email',
        'project_name',
        'project_id',
        'role',
        'password',
        'status',
        'user_id',
        'approved_by',
        'approved_at',
    ];

    protected $hidden = [
        'password',
    ];

    protected function casts(): array
    {
        return [
            'approved_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * Whether the applicant typed a project name manually ("Other" option)
     * instead of picking one from the projects list.
     */
    public function usesCustomProject(): bool
    {
        return $this->project_id === null;
    }

    /**
     * Whether the applicant chose their own password at registration time.
     * Legacy registrations (before the password field existed) return false.
     */
    public function hasPassword(): bool
    {
        return ! empty($this->getRawOriginal('password'));
    }

    public function roleLabel(): string
    {
        return match ($this->role) {
            'project-manager' => 'Project Manager',
            'user' => 'User',
            default => $this->role,
        };
    }
}
