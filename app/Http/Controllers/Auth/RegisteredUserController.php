<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\User;
use App\Models\UserRegistration;
use App\Notifications\NewUserRegistration;
use Closure;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        $projects = Project::query()->orderBy('name')->pluck('name', 'id');

        return view('auth.register', compact('projects'));
    }

    /**
     * Handle an incoming registration request.
     * Stores a pending registration (with the applicant's chosen password, hashed)
     * for admin approval. The user signs in with this password once approved.
     */
    public function store(Request $request): RedirectResponse
    {
        // Normalize the email: trim whitespace and convert to lowercase so the
        // address is accepted regardless of how it is typed, and duplicate
        // detection is case-insensitive.
        $request->merge([
            'email' => mb_strtolower(trim((string) $request->input('email'))),
        ]);

        // 'other' (or an empty value) means the applicant typed a custom project name.
        $isCustomProject = $request->input('project_id') === 'other' || ! $request->filled('project_id');

        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                // Mandatory format: the address must contain "@" and end with ".com".
                function (string $attribute, mixed $value, Closure $fail) {
                    $value = mb_strtolower(trim((string) $value));
                    if (! str_contains($value, '@') || ! str_ends_with($value, '.com')) {
                        $fail('The email address must contain "@" and end with ".com".');
                    }
                },
                // An approved account with this email already exists.
                function (string $attribute, mixed $value, Closure $fail) {
                    if (User::where('email', $value)->exists()) {
                        $fail('This email address is already registered and cannot be duplicated. Please try signing in instead.');
                    }
                },
                // Another access request with this email is already waiting for approval.
                function (string $attribute, mixed $value, Closure $fail) {
                    if (UserRegistration::where('email', $value)->where('status', 'pending')->exists()) {
                        $fail('This email address has already been used to request access and cannot be duplicated. Please wait for your IT Admin to approve it.');
                    }
                },
            ],
            'role' => ['required', Rule::in(['user', 'project-manager'])],
            'password' => ['required', 'confirmed', Password::defaults()],
        ];

        if ($isCustomProject) {
            $rules['project_name'] = ['required', 'string', 'max:255'];
        } else {
            $rules['project_id'] = ['required', 'integer', 'exists:projects,id'];
        }

        $validated = $request->validate($rules);

        if ($isCustomProject) {
            $projectName = trim($validated['project_name']);

            // Refuse duplicates of projects that already exist — the applicant should pick from the list.
            $duplicate = Project::whereRaw('LOWER(name) = ?', [mb_strtolower($projectName)])->exists();
            if ($duplicate) {
                return back()
                    ->withErrors(['project_name' => 'This project already exists — please choose it from the list.'])
                    ->withInput();
            }

            $projectId = null;
        } else {
            $project = Project::findOrFail((int) $validated['project_id']);
            $projectName = $project->name;
            $projectId = $project->id;
        }

        $registration = UserRegistration::create([
            'name' => $request->name,
            'email' => $request->email,
            'project_name' => $projectName,
            'project_id' => $projectId,
            'role' => $request->role,
            'password' => $request->password, // hashed automatically via model cast
            'status' => 'pending',
        ]);

        // Notify every admin user about the new registration so they can approve it.
        $admins = User::role('admin')->get();
        foreach ($admins as $admin) {
            $admin->notify(new NewUserRegistration($registration));
        }

        return redirect()
            ->route('register')
            ->with('status', "Please contact your Organization's IT Admin to Access the System");
    }
}
