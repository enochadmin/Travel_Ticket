<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

/**
 * Tracks when the signed-in user was last active (drives the Online /
 * "Inactive · active X ago" presence shown on the profile popup).
 *
 * The write is throttled to once per minute and skipped entirely while an
 * admin is impersonating (so the impersonated user is not marked as online).
 */
class UpdateLastActivity
{
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check() && ! session()->has('impersonator_id')) {
            $user = Auth::user();

            $shouldWrite = $user->last_activity_at === null
                || $user->last_activity_at->lte(now()->subMinute());

            if ($shouldWrite) {
                DB::table('users')
                    ->where('id', $user->getAuthIdentifier())
                    ->update(['last_activity_at' => now()]);
            }
        }

        return $next($request);
    }
}
