<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserRegistration;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
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

        $user = User::create([
            'name' => $userRegistration->name,
            'email' => $userRegistration->email,
            'password' => 'password',
            'must_change_password' => true,
        ]);

        $user->assignRole($userRegistration->role);

        $userRegistration->update([
            'status' => 'approved',
            'user_id' => $user->id,
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);

        return redirect()
            ->route('user-registrations.index')
            ->with(
                'success',
                "Registration approved. {$user->name} can sign in with the default password \"password\" and must change it on first login. Assign their project via User Management → Edit."
            );
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
}
