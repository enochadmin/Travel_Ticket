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
        $sessionDriver = config('session.driver');
        $usesDatabase = $sessionDriver === 'database' && Schema::hasTable('sessions');
        $sessions = collect();

        if ($usesDatabase) {
            $activeSince = now()->subMinutes((int) config('session.lifetime', 120))->timestamp;

            $rawSessions = DB::table('sessions')
                ->where('last_activity', '>=', $activeSince)
                ->orderByDesc('last_activity')
                ->get();

            $userIds = $rawSessions->pluck('user_id')->filter()->unique();
            $users = User::with('roles')
                ->whereIn('id', $userIds)
                ->get()
                ->keyBy('id');

            $sessions = $rawSessions
                ->map(function ($session) use ($users, $currentSessionId) {
                    $user = $users->get($session->user_id) ?? $this->resolveUserFromPayload($session->payload);

                    if (! $user) {
                        return null;
                    }

                    $session->user = $user;
                    $session->is_current = hash_equals((string) $currentSessionId, (string) $session->id);
                    $session->last_activity_at = Carbon::createFromTimestamp($session->last_activity);
                    $session->status = $session->last_activity_at->greaterThanOrEqualTo(now()->subMinutes(5))
                        ? 'Active'
                        : 'Idle';
                    $session->browser = $this->parseBrowser($session->user_agent);

                    return $session;
                })
                ->filter()
                ->values();
        }

        return view('settings.session.show', compact(
            'sessions',
            'currentSessionId',
            'sessionDriver',
            'usesDatabase'
        ));
    }

    public function destroy(Request $request, string $sessionId)
    {
        if ($this->isCurrentSession($sessionId, $request->session()->getId())) {
            return redirect()
                ->route('settings.session.show')
                ->with('error', 'You cannot terminate your own active admin session from here.');
        }

        $deleted = $this->terminateSessions([$sessionId], $request->session()->getId());

        return redirect()
            ->route('settings.session.show')
            ->with($deleted > 0 ? 'success' : 'error', $deleted > 0
                ? 'User session terminated successfully. They will need to sign in again.'
                : 'No session was terminated.');
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
                ? "{$deleted} user session(s) terminated successfully."
                : 'No sessions were terminated.');
    }

    private function terminateSessions(array $sessionIds, string $currentSessionId): int
    {
        if (config('session.driver') !== 'database' || ! Schema::hasTable('sessions')) {
            return 0;
        }

        return DB::table('sessions')
            ->whereIn('id', $sessionIds)
            ->where('id', '!=', $currentSessionId)
            ->delete();
    }

    private function isCurrentSession(string $sessionId, string $currentSessionId): bool
    {
        return hash_equals($currentSessionId, $sessionId);
    }

    private function resolveUserFromPayload(?string $payload): ?User
    {
        if (! $payload) {
            return null;
        }

        try {
            $data = @unserialize(base64_decode($payload, true));

            if (! is_array($data)) {
                return null;
            }

            foreach ($data as $key => $value) {
                if (is_string($key) && str_starts_with($key, 'login_web_') && is_numeric($value)) {
                    return User::with('roles')->find((int) $value);
                }
            }
        } catch (\Throwable) {
            return null;
        }

        return null;
    }

    private function parseBrowser(?string $userAgent): string
    {
        if (! $userAgent) {
            return 'Unknown browser';
        }

        $ua = strtolower($userAgent);

        $browser = match (true) {
            str_contains($ua, 'edg/') => 'Microsoft Edge',
            str_contains($ua, 'chrome/') && ! str_contains($ua, 'edg/') => 'Google Chrome',
            str_contains($ua, 'firefox/') => 'Mozilla Firefox',
            str_contains($ua, 'safari/') && ! str_contains($ua, 'chrome/') => 'Safari',
            str_contains($ua, 'opr/') || str_contains($ua, 'opera') => 'Opera',
            default => 'Unknown browser',
        };

        $platform = match (true) {
            str_contains($ua, 'windows') => 'Windows',
            str_contains($ua, 'mac os') || str_contains($ua, 'macintosh') => 'macOS',
            str_contains($ua, 'android') => 'Android',
            str_contains($ua, 'iphone') || str_contains($ua, 'ipad') => 'iOS',
            str_contains($ua, 'linux') => 'Linux',
            default => 'Unknown OS',
        };

        return "{$browser} on {$platform}";
    }
}
