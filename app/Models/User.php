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
use Illuminate\Support\Facades\Storage;

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
        'phone',
        'avatar_path',
        'last_activity_at',
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
            'last_activity_at' => 'datetime',
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

    /** Two-letter avatar initials: first letter of the first and last name. */
    public function initials(): string
    {
        $words = preg_split('/\s+/', trim((string) $this->name)) ?: [];
        $first = (string) ($words[0] ?? '');
        $last = count($words) > 1 ? (string) $words[count($words) - 1] : '';

        return strtoupper(mb_substr($first, 0, 1) . ($last !== '' ? mb_substr($last, 0, 1) : ''));
    }

    /**
     * Relative path of the profile picture on the "public" disk.
     *
     * Legacy rows may store an absolute URL, or a path that already contains the
     * "storage/" prefix, so those forms are normalised here. Returns null when the
     * value is unusable (empty or an external URL we cannot manage on disk).
     */
    public function avatarDiskPath(): ?string
    {
        $raw = trim((string) $this->avatar_path);

        if ($raw === '' || preg_match('#^https?://#i', $raw)) {
            return null;
        }

        $path = preg_replace('#^/?storage/#i', '', $raw);
        $path = preg_replace('#^/?public/#i', '', (string) $path);
        $path = ltrim((string) $path, '/');

        return $path === '' ? null : $path;
    }

    /** Whether the stored profile picture actually exists on disk. */
    public function hasAvatarFile(): bool
    {
        $path = $this->avatarDiskPath();

        if ($path === null) {
            return false;
        }

        try {
            return Storage::disk('public')->exists($path);
        } catch (\Throwable) {
            return false;
        }
    }

    /**
     * Public URL of the uploaded profile picture, if any.
     *
     * Returns null when there is no picture *or* when the file is gone, so views fall
     * back to the initials placeholder instead of rendering a broken image.
     */
    public function avatarUrl(): ?string
    {
        $raw = trim((string) $this->avatar_path);

        // Legacy rows may already hold an absolute URL — use it untouched.
        if ($raw !== '' && preg_match('#^https?://#i', $raw)) {
            return $raw;
        }

        $path = $this->avatarDiskPath();

        if ($path === null || ! $this->hasAvatarFile()) {
            return null;
        }

        return self::publicStorageUrl($path);
    }

    /**
     * Build a URL for a file on the "public" disk.
     *
     * Uses the /storage symlink when it is available (fast, served straight by the web
     * server) and falls back to the authenticated media route otherwise — a missing
     * `php artisan storage:link` must not produce broken images.
     */
    public static function publicStorageUrl(string $path): string
    {
        $encoded = implode('/', array_map('rawurlencode', explode('/', $path)));

        if (self::hasPublicStorageLink()) {
            return asset('storage/' . $encoded);
        }

        return route('media.avatars', ['path' => $encoded]);
    }

    /** Cached per request: does public/storage resolve to storage/app/public? */
    protected static function hasPublicStorageLink(): bool
    {
        static $available = null;

        return $available ??= file_exists(public_path('storage'));
    }

    /** Online when the account is active and active within the last N minutes. */
    public function isOnline(int $withinMinutes = 5): bool
    {
        return $this->isActive()
            && $this->last_activity_at !== null
            && $this->last_activity_at->gte(now()->subMinutes($withinMinutes));
    }

    /** Human presence label: "Online" or "Inactive · active X ago". */
    public function presenceLabel(): string
    {
        if ($this->isOnline()) {
            return 'Online';
        }

        return $this->last_activity_at
            ? 'Inactive · active ' . $this->last_activity_at->diffForHumans()
            : 'Inactive';
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
