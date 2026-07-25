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
        'role',
        'status',
        'user_id',
        'approved_by',
        'approved_at',
    ];

    protected function casts(): array
    {
        return [
            'approved_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
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
