<?php

namespace App\Http\Controllers;

use App\Models\ImpersonationLog;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

/**
 * Admin "Open as" impersonation (view-only preview of another user's account).
 */
class ImpersonationController extends Controller
{
    public function start(Request $request, User $user): RedirectResponse
    {
        $admin = Auth::user();

        abort_unless($admin && $admin->hasRole('admin'), 403, 'Only administrators can use "Open as".');
        abort_if((int) $user->id === (int) $admin->id, 403, 'You cannot open your own account.');
        abort_if($user->hasRole('admin'), 403, 'Administrator accounts cannot be opened.');
        abort_if(! $user->isActive(), 403, 'This user is inactive and cannot be opened.');

        session()->put([
            'impersonator_id' => $admin->id,
            'impersonated_id' => $user->id,
        ]);

        Auth::login($user);

        ImpersonationLog::create([
            'admin_id' => $admin->id,
            'target_id' => $user->id,
            'action' => 'start',
            'ip_address' => $request->ip(),
            'user_agent' => substr((string) $request->userAgent(), 0, 500),
        ]);

        return redirect()->route('dashboard')->with(
            'success',
            'Now viewing as ' . $user->name . ' (read-only). Use Exit to return to your administrator view.'
        );
    }

    public function exit(Request $request): RedirectResponse
    {
        $adminId = session('impersonator_id');
        $targetId = session('impersonated_id');

        $admin = $adminId ? User::find($adminId) : null;
        if ($admin) {
            Auth::login($admin);
        }

        session()->forget(['impersonator_id', 'impersonated_id']);

        if ($adminId || $targetId) {
            ImpersonationLog::create([
                'admin_id' => $adminId,
                'target_id' => $targetId,
                'action' => 'exit',
                'ip_address' => $request->ip(),
                'user_agent' => substr((string) $request->userAgent(), 0, 500),
            ]);
        }

        return redirect()->route('dashboard')->with(
            'success',
            'Exited "Open as". You are back on your administrator view.'
        );
    }

    public function logs(): View
    {
        $logs = ImpersonationLog::with(['admin', 'target'])
            ->latest()
            ->paginate(25);

        return view('settings.impersonation-logs', compact('logs'));
    }
}
