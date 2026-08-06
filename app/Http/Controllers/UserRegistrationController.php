<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\User;
use App\Models\UserRegistration;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class UserRegistrationController extends Controller
{
    public function index(Request $request): View
    {
        $status = $request->get('status', 'pending');

        $query = UserRegistration::with(['user', 'approver'])
            ->latest();

        if (in_array($status, ['pending', 'approved', 'rejected'], true)) {
            $query->where('status', $status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%'.$search.'%')
                    ->orWhere('email', 'like', '%'.$search.'%')
                    ->orWhere('project_name', 'like', '%'.$search.'%');
            });
        }

        $registrations = $query->paginate(15)->withQueryString();

        $pendingCount = UserRegistration::where('status', 'pending')->count();

        return view('user-registrations.index', compact('registrations', 'status', 'pendingCount'));
    }

    public function approve(UserRegistration $userRegistration): RedirectResponse
    {
        if (! $userRegistration->isPending()) {
            return redirect()
                ->route('user-registrations.index')
                ->with('error', 'This registration has already been processed.');
        }

        if (User::where('email', $userRegistration->email)->exists()) {
            return redirect()
                ->route('user-registrations.index')
                ->with('error', 'A user with this email already exists. Resolve the conflict before approving.');
        }

        $selfRegistered = $userRegistration->hasPassword();

        $user = $this->createUserFromRegistration($userRegistration, $userRegistration->project_id);

        $userRegistration->update([
            'status' => 'approved',
            'user_id' => $user->id,
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);

        if ($userRegistration->project_id) {
            $message = $selfRegistered
                ? "Registration approved. {$user->name} is linked to the {$userRegistration->project->name} project and can sign in with the password they chose at registration."
                : "Registration approved. {$user->name} is linked to the {$userRegistration->project->name} project and must change the default password on first login.";
        } else {
            $message = $selfRegistered
                ? "Registration approved. {$user->name} can sign in with the password they chose at registration. Assign their project via User Management → Edit."
                : "Registration approved. {$user->name} can sign in with the default password \"password\" and must change it on first login. Assign their project via User Management → Edit.";
        }

        return redirect()
            ->route('user-registrations.index')
            ->with('success', $message);
    }

    /**
     * Approve a registration whose applicant typed a custom project name ("Other").
     * Creates the project in the system (unless a project with that name already
     * exists), links the new user to it, and approves the registration.
     */
    public function approveWithProject(UserRegistration $userRegistration): RedirectResponse
    {
        if (! $userRegistration->isPending()) {
            return redirect()
                ->route('user-registrations.index')
                ->with('error', 'This registration has already been processed.');
        }

        if (User::where('email', $userRegistration->email)->exists()) {
            return redirect()
                ->route('user-registrations.index')
                ->with('error', 'A user with this email already exists. Resolve the conflict before approving.');
        }

        $projectName = trim($userRegistration->project_name);

        $existing = Project::whereRaw('LOWER(name) = ?', [mb_strtolower($projectName)])->first();

        $project = $existing ?? Project::create([
            'name' => $projectName,
            'status' => 'active',
            'description' => "Added automatically from user registration approval ({$userRegistration->name}, {$userRegistration->email}).",
        ]);

        $user = $this->createUserFromRegistration($userRegistration, $project->id);

        $userRegistration->update([
            'status' => 'approved',
            'user_id' => $user->id,
            'project_id' => $project->id,
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);

        $projectAction = $existing
            ? 'was linked to the existing project'
            : 'was added as the new project';

        $message = $userRegistration->hasPassword()
            ? "Registration approved. {$user->name} {$projectAction} \"{$project->name}\" and can sign in with the password they chose at registration."
            : "Registration approved. {$user->name} {$projectAction} \"{$project->name}\" and must change the default password on first login.";

        return redirect()
            ->route('user-registrations.index')
            ->with('success', $message);
    }

    public function reject(UserRegistration $userRegistration): RedirectResponse
    {
        if (! $userRegistration->isPending()) {
            return redirect()
                ->route('user-registrations.index')
                ->with('error', 'This registration has already been processed.');
        }

        $userRegistration->update([
            'status' => 'rejected',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);

        return redirect()
            ->route('user-registrations.index')
            ->with('success', 'Registration rejected.');
    }

    /**
     * Create the approved User record from a registration.
     * Links the user to $projectId (users.project_id + members pivot) when provided.
     */
    private function createUserFromRegistration(UserRegistration $userRegistration, ?int $projectId): User
    {
        $selfRegistered = $userRegistration->hasPassword();

        $user = User::create([
            'name' => $userRegistration->name,
            'email' => $userRegistration->email,
            // Temporary value for legacy registrations; overwritten below for self-registered users.
            'password' => 'password',
            'project_id' => $projectId,
            // Self-registered users chose their own password — no forced change needed.
            'must_change_password' => ! $selfRegistered,
        ]);

        if ($selfRegistered) {
            // The registration already stores a bcrypt hash. Write it directly to
            // avoid the User model's 'hashed' cast re-hashing it (double-hash).
            DB::table('users')
                ->where('id', $user->id)
                ->update(['password' => $userRegistration->getRawOriginal('password')]);
        }

        $user->assignRole($userRegistration->role);

        if ($projectId) {
            $user->projects()->syncWithoutDetaching([$projectId]);
        }

        return $user;
    }
}
