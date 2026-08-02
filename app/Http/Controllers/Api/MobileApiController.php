<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

abstract class MobileApiController extends Controller
{
    protected function authenticatedUser(Request $request): User|JsonResponse
    {
        $header = (string) $request->bearerToken();
        $decoded = base64_decode($header, true);

        if (! $decoded || ! str_contains($decoded, '|')) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        [$userId, $signature] = explode('|', $decoded, 2);
        $user = User::with('roles')->find($userId);

        if (! $user || ! hash_equals($this->signatureFor($user), $signature)) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        Auth::setUser($user);

        return $user;
    }

    protected function tokenFor(User $user): string
    {
        return base64_encode($user->id . '|' . $this->signatureFor($user));
    }

    protected function serializeUser(User $user): array
    {
        $user->loadMissing('roles');

        return [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'roles' => $user->roles->pluck('name')->values(),
        ];
    }

    private function signatureFor(User $user): string
    {
        return hash_hmac(
            'sha256',
            $user->id . '|' . $user->password,
            (string) config('app.key')
        );
    }
}
