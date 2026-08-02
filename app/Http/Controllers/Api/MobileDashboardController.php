<?php

namespace App\Http\Controllers\Api;

use App\Models\Project;
use App\Models\TravelRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MobileDashboardController extends MobileApiController
{
    public function index(Request $request): JsonResponse
    {
        $user = $this->authenticatedUser($request);

        if ($user instanceof JsonResponse) {
            return $user;
        }

        if ($user->hasRole('admin')) {
            return response()->json(['data' => $this->adminStats()]);
        }

        if ($user->hasRole('commercial-director')) {
            return response()->json(['data' => $this->commercialDirectorStats()]);
        }

        if ($user->hasRole('project-manager')) {
            return response()->json(['data' => $this->projectManagerStats($user)]);
        }

        return response()->json(['data' => $this->employeeStats($user)]);
    }

    private function adminStats(): array
    {
        $pending = TravelRequest::whereIn('status', [
            'pending_pm',
            'pending_commercial',
            'pending_hod',
            'pending_ceo',
        ])->count();

        return [
            'role' => 'admin',
            'title' => 'Admin Overview',
            'subtitle' => 'Organization-wide travel desk metrics.',
            'total_users' => User::count(),
            'total_projects' => Project::count(),
            'active_projects' => Project::where('status', 'active')->count(),
            'unassigned_users' => User::whereNull('project_id')->count(),
            'total_requests' => TravelRequest::count(),
            'approved' => TravelRequest::where('status', 'approved')->count(),
            'rejected' => TravelRequest::where('status', 'rejected')->count(),
            'pending' => $pending,
            'recent_requests' => $this->recentRequests(),
            'latest_users' => User::with('roles')->latest()->take(5)->get()->map(fn (User $u) => [
                'id' => $u->id,
                'name' => $u->name,
                'email' => $u->email,
                'roles' => $u->roles->pluck('name')->values(),
            ])->values(),
        ];
    }

    private function commercialDirectorStats(): array
    {
        $pendingCommercial = TravelRequest::where('status', 'pending_commercial')->count();
        $pending = TravelRequest::whereIn('status', [
            'pending_pm',
            'pending_commercial',
            'pending_hod',
            'pending_ceo',
        ])->count();

        $topDestinations = TravelRequest::selectRaw('destination, COUNT(*) as count')
            ->whereNotNull('destination')
            ->where('destination', '!=', '')
            ->groupBy('destination')
            ->orderByRaw('COUNT(*) DESC')
            ->limit(5)
            ->get()
            ->map(fn ($row) => [
                'name' => $row->destination,
                'count' => (int) $row->count,
            ])
            ->values();

        return [
            'role' => 'commercial-director',
            'title' => 'Commercial Director',
            'subtitle' => 'Summary of all travel requests across the organization.',
            'total_requests' => TravelRequest::count(),
            'approved' => TravelRequest::where('status', 'approved')->count(),
            'rejected' => TravelRequest::where('status', 'rejected')->count(),
            'pending' => $pending,
            'pending_commercial' => $pendingCommercial,
            'top_destinations' => $topDestinations,
            'recent_requests' => $this->recentRequests(8),
            'queue' => TravelRequest::with(['user.roles', 'project'])
                ->where('status', 'pending_commercial')
                ->latest()
                ->take(5)
                ->get()
                ->map(fn (TravelRequest $r) => $this->serializeTravelRequest($r))
                ->values(),
        ];
    }

    private function projectManagerStats(User $user): array
    {
        $pmProjectId = $user->approverProjectId();
        $project = $pmProjectId ? Project::find($pmProjectId) : null;

        $baseQuery = fn () => TravelRequest::where('project_id', $pmProjectId ?? -1);

        $pendingPm = $baseQuery()->where('status', 'pending_pm')->count();

        return [
            'role' => 'project-manager',
            'title' => $project?->name ?? 'My Project',
            'subtitle' => 'Summary of travel requests for your project team.',
            'project' => $project ? [
                'id' => $project->id,
                'name' => $project->name,
                'project_code' => $project->project_code,
                'location' => $project->location,
                'status' => $project->status,
            ] : null,
            'total_requests' => $baseQuery()->count(),
            'approved' => $baseQuery()->where('status', 'approved')->count(),
            'rejected' => $baseQuery()->where('status', 'rejected')->count(),
            'pending' => $baseQuery()->whereIn('status', [
                'pending_pm',
                'pending_commercial',
                'pending_hod',
                'pending_ceo',
            ])->count(),
            'pending_pm' => $pendingPm,
            'recent_requests' => $baseQuery()
                ->with(['user.roles', 'project'])
                ->latest()
                ->take(8)
                ->get()
                ->map(fn (TravelRequest $r) => $this->serializeTravelRequest($r))
                ->values(),
            'queue' => $baseQuery()
                ->with(['user.roles', 'project'])
                ->where('status', 'pending_pm')
                ->latest()
                ->take(5)
                ->get()
                ->map(fn (TravelRequest $r) => $this->serializeTravelRequest($r))
                ->values(),
        ];
    }

    private function employeeStats(User $user): array
    {
        $baseQuery = fn () => TravelRequest::where('user_id', $user->id);

        return [
            'role' => 'employee',
            'title' => 'My Travel',
            'subtitle' => 'Track your submitted travel requests.',
            'total_requests' => $baseQuery()->count(),
            'approved' => $baseQuery()->where('status', 'approved')->count(),
            'rejected' => $baseQuery()->where('status', 'rejected')->count(),
            'pending' => $baseQuery()->whereIn('status', [
                'pending_pm',
                'pending_commercial',
                'pending_hod',
                'pending_ceo',
            ])->count(),
            'recent_requests' => $baseQuery()
                ->with(['user.roles', 'project'])
                ->latest()
                ->take(8)
                ->get()
                ->map(fn (TravelRequest $r) => $this->serializeTravelRequest($r))
                ->values(),
        ];
    }

    private function recentRequests(int $limit = 6): array
    {
        return TravelRequest::with(['user.roles', 'project'])
            ->latest()
            ->take($limit)
            ->get()
            ->map(fn (TravelRequest $r) => $this->serializeTravelRequest($r))
            ->values()
            ->all();
    }

    private function serializeTravelRequest(TravelRequest $travelRequest): array
    {
        return [
            'id' => $travelRequest->id,
            'origin' => $travelRequest->origin,
            'destination' => $travelRequest->destination,
            'passenger_count' => $travelRequest->passenger_count,
            'flight_type' => $travelRequest->flight_type,
            'travel_date' => $this->serializeDate($travelRequest->travel_date),
            'return_date' => $this->serializeDate($travelRequest->return_date),
            'purpose' => $travelRequest->purpose,
            'remarks' => $travelRequest->remarks,
            'status' => $travelRequest->status,
            'project' => $travelRequest->project ? [
                'id' => $travelRequest->project->id,
                'name' => $travelRequest->project->name,
                'project_code' => $travelRequest->project->project_code,
                'location' => $travelRequest->project->location,
                'status' => $travelRequest->project->status,
            ] : null,
            'user' => $travelRequest->user ? $this->serializeUser($travelRequest->user) : null,
        ];
    }

    private function serializeDate(mixed $value): ?string
    {
        if (! $value) {
            return null;
        }

        return method_exists($value, 'format') ? $value->format('Y-m-d') : (string) $value;
    }
}
