<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserRegistration;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     * Stores a pending registration for admin approval (no login / no password yet).
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                'unique:'.User::class,
                Rule::unique('user_registrations', 'email')->where(fn ($query) => $query->where('status', 'pending')),
            ],
            'project_name' => ['required', 'string', 'max:255'],
            'role' => ['required', Rule::in(['user', 'project-manager'])],
        ]);

        UserRegistration::create([
            'name' => $request->name,
            'email' => $request->email,
            'project_name' => $request->project_name,
            'role' => $request->role,
            'status' => 'pending',
        ]);

        return redirect()
            ->route('register')
            ->with('status', "Please contact your Organization's IT Admin to Access the System");
    }
}
