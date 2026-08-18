<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\User;
use App\Models\UserRegistration;
use App\Notifications\NewUserRegistration;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
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
        // 'other' (or an empty value) means the applicant typed a custom project name.
        $isCustomProject = $request->input('project_id') === 'other' || ! $request->filled('project_id');

        $rules = [
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
