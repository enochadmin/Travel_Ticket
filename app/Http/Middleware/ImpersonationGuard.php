<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Enforces the admin "Open as" (impersonation) session.
 *
 * While an impersonation is active:
 *  - every request is served as the impersonated user (session login already switched);
 *  - the app is VIEW-ONLY: all mutating requests (POST/PUT/PATCH/DELETE) are blocked
 *    except the explicit Exit action and the Sign-out action, which both end the
 *    impersonation and restore the original admin instead of logging anyone out;
 *  - if the impersonated user no longer exists, is an admin, or is inactive, the
 *    impersonation is cleaned up automatically and the admin session is restored.
 */
class ImpersonationGuard
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! session()->has('impersonator_id') || ! session()->has('impersonated_id')) {
            return $next($request);
        }

        $admin = User::find(session('impersonator_id'));
        $target = User::find(session('impersonated_id'));

        $valid = $admin && $target && ! $target->hasRole('admin') && $target->isActive();

        if (! $valid) {
            $this->endImpersonation($request, 'exit');
            return redirect()->route('dashboard')
                ->with('error', 'The "Open as" session was no longer valid and has been ended.');
        }

        $routeName = optional($request->route())->getName();

        // Signing out while impersonating must only exit back to the admin account.
        if ($routeName === 'logout') {
            $this->endImpersonation($request, 'exit');
            return redirect()->route('dashboard')
                ->with('success', 'Exited "Open as". You are back on your administrator view.');
        }

        // View-only enforcement: only the explicit Exit action may mutate state.
        if ($routeName !== 'impersonation.exit'
            && ! in_array($request->method(), ['GET', 'HEAD', 'OPTIONS'], true)) {
            if ($request->expectsJson()) {
                abort(403, 'View-only mode: changes are disabled while using "Open as". Click Exit to return to your admin view.');
            }

            return redirect()->back()->with(
                'error',
                'View-only mode: you are viewing this page through "Open as" and cannot make changes. Click Exit to return to your administrator view.'
            );
        }

        return $next($request);
    }

    /**
     * Restore the original admin session and record an audit entry.
     */
    private function endImpersonation(Request $request, string $action): void
    {
        $adminId = session('impersonator_id');
        $targetId = session('impersonated_id');

        $admin = $adminId ? User::find($adminId) : null;
        if ($admin) {
            Auth::login($admin);
        }

        session()->forget(['impersonator_id', 'impersonated_id']);

        \App\Models\ImpersonationLog::create([
            'admin_id' => $adminId,
            'target_id' => $targetId,
            'action' => $action,
            'ip_address' => $request->ip(),
            'user_agent' => substr((string) $request->userAgent(), 0, 500),
        ]);
    }
}
