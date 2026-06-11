<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class ForcePasswordChangeController extends Controller
{
    public function create(): View
    {
        return view('auth.change-password-required');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'password' => [
                'required',
                'confirmed',
                Password::defaults(),
                function (string $attribute, mixed $value, \Closure $fail): void {
                    if ($value === 'password') {
                        $fail('Please choose a password different from the default temporary password.');
                    }
                },
            ],
        ]);

        $request->user()->update([
            'password' => $validated['password'],
            'must_change_password' => false,
        ]);

        return redirect()->route('dashboard')->with('status', 'password-updated');
    }
}
