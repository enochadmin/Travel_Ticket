<?php

namespace App\Http\Controllers;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class SettingsSessionController extends Controller
{
    public function show(Request $request)
    {
        $currentSessionId = $request->session()->getId();
        $sessions = collect();

        if (Schema::hasTable('sessions')) {
            $activeSince = now()->subMinutes((int) config('session.lifetime', 120))->timestamp;

            $sessions = DB::table('sessions')
                ->whereNotNull('user_id')
                ->where('last_activity', '>=', $activeSince)
                ->orderByDesc('last_activity')
                ->get();

            $users = User::with('roles')
                ->whereIn('id', $sessions->pluck('user_id')->filter()->unique())
                ->get()
                ->keyBy('id');

            $sessions = $sessions->map(function ($session) use ($users, $currentSessionId) {
                $session->user = $users->get($session->user_id);
                $session->is_current = hash_equals((string) $currentSessionId, (string) $session->id);
                $session->last_activity_at = Carbon::createFromTimestamp($session->last_activity);
                $session->status = $session->last_activity_at->greaterThanOrEqualTo(now()->subMinutes(5))
                    ? 'Active'
                    : 'Idle';

                return $session;
            })->filter(fn ($session) => $session->user)->values();
        }

        return view('settings.session.show', compact('sessions', 'currentSessionId'));
    }

    public function destroy(Request $request, string $sessionId)
    {
        $deleted = $this->terminateSessions([$sessionId], $request->session()->getId());

        return redirect()
            ->route('settings.session.show')
            ->with($deleted > 0 ? 'success' : 'error', $deleted > 0
                ? 'Employee session terminated successfully.'
                : 'No employee session was terminated.');
    }

    public function bulkDestroy(Request $request)
    {
        $validated = $request->validate([
            'session_ids' => ['required', 'array', 'min:1'],
            'session_ids.*' => ['string'],
        ]);

        $deleted = $this->terminateSessions($validated['session_ids'], $request->session()->getId());

        return redirect()
            ->route('settings.session.show')
            ->with($deleted > 0 ? 'success' : 'error', $deleted > 0
                ? "{$deleted} employee session(s) terminated successfully."
                : 'No employee sessions were terminated.');
    }

    private function terminateSessions(array $sessionIds, string $currentSessionId): int
    {
        if (! Schema::hasTable('sessions')) {
            return 0;
        }

        return DB::table('sessions')
            ->whereIn('id', $sessionIds)
            ->where('id', '!=', $currentSessionId)
            ->delete();
    }
}
