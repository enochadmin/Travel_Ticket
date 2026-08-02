<?php

namespace App\Http\Controllers\Api;

use App\Models\City;
use App\Models\Project;
use App\Models\TravelRequest;
use App\Models\User;
use App\Notifications\TicketStatusUpdated;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use Illuminate\Validation\Rule;

class MobileTravelRequestController extends MobileApiController
{
    public function projects(Request $request): JsonResponse
    {
        $user = $this->authenticatedUser($request);

        if ($user instanceof JsonResponse) {
            return $user;
        }

        if ($user->hasAnyRole(['admin', 'ceo', 'head-office-director', 'commercial-director'])) {
            $projects = Project::orderBy('name')->get();
        } else {
            $projectIds = $user->memberProjectIds();
            $projects = Project::whereIn('id', $projectIds)->orderBy('name')->get();
        }

        return response()->json([
            'data' => $projects->map(fn (Project $project) => $this->serializeProject($project))->values(),
        ]);
    }

    public function cities(Request $request): JsonResponse
    {
        $user = $this->authenticatedUser($request);

        if ($user instanceof JsonResponse) {
            return $user;
        }

        $cities = City::active()->ordered()->get(['name', 'region']);

        return response()->json(['data' => $cities]);
    }

    public function index(Request $request): JsonResponse
    {
        $user = $this->authenticatedUser($request);

        if ($user instanceof JsonResponse) {
            return $user;
        }

        $query = TravelRequest::with(['user.roles', 'project']);
        $viewType = $request->query('view');

        if ($user->hasAnyRole(['admin', 'ceo', 'head-office-director', 'commercial-director'])) {
            if ($user->hasRole('commercial-director') && $viewType === 'personal') {
                $query->where('user_id', $user->id);
            } elseif ($user->hasRole('commercial-director') && $viewType === 'approved') {
                $query->where('status', 'pending_commercial');
            }
        } elseif ($user->hasRole('project-manager')) {
            if ($viewType === 'personal') {
                $query->where('user_id', $user->id);
            } else {
                $query->where('project_id', $user->approverProjectId() ?? -1);
            }
        } else {
            $query->where('user_id', $user->id);
        }

        if ($status = $request->query('status')) {
            if ($status === 'pending') {
                $query->whereIn('status', [
                    'pending_pm',
                    'pending_commercial',
                    'pending_hod',
                    'pending_ceo',
                ]);
            } else {
                $query->where('status', $status);
            }
        }

        $requests = $query->latest()->take(50)->get();

        return response()->json([
            'data' => $requests->map(fn (TravelRequest $travelRequest) => $this->serializeTravelRequest($travelRequest))->values(),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $user = $this->authenticatedUser($request);

        if ($user instanceof JsonResponse) {
            return $user;
        }

        if ($user->hasRole('reception')) {
            return response()->json(['message' => 'Reception users cannot create travel requests.'], 403);
        }

        $validated = $request->validate([
            'project_id' => ['required', 'exists:projects,id'],
            'destination' => ['required', 'string', 'max:255', Rule::exists('cities', 'name')->where('is_active', true)],
            'origin' => ['required', 'string', 'max:255', Rule::exists('cities', 'name')->where('is_active', true)],
            'passenger_count' => ['required', 'integer', 'min:1', 'max:50'],
            'flight_type' => ['required', Rule::in(['national', 'international'])],
            'travel_date' => ['required', 'date'],
            'return_date' => ['nullable', 'date', 'after_or_equal:travel_date'],
            'purpose' => ['required', 'string'],
            'remarks' => ['nullable', 'string'],
        ]);

        $additionalCount = max(0, (int) $validated['passenger_count'] - 1);
        if ($additionalCount > 0) {
            $request->validate([
                'additional_passengers' => ['required', 'array', 'size:'.$additionalCount],
                'additional_passengers.*' => ['required', 'string', 'max:255'],
            ]);
            $validated['additional_passengers'] = array_values($request->input('additional_passengers', []));
        } else {
            $validated['additional_passengers'] = null;
        }

        $isPrivileged = $user->hasAnyRole(['admin', 'head-office-director', 'commercial-director', 'ceo']);

        if (! $isPrivileged) {
            $allowedProjectIds = $user->memberProjectIds();

            if ($allowedProjectIds->isEmpty() || ! $allowedProjectIds->contains((int) $validated['project_id'])) {
                return response()->json([
                    'message' => 'You can only request travel for a project you are a member of.',
                ], 403);
            }
        }

        $travelRequest = new TravelRequest($validated);
        $travelRequest->user_id = $user->id;
        $travelRequest->status = $user->hasRole('ceo')
            ? 'approved'
            : ($user->hasRole('project-manager') ? 'pending_commercial' : 'pending_pm');
        $travelRequest->save();
        $travelRequest->load(['user.roles', 'project']);

        $this->sendCreatedNotifications($travelRequest, $user);

        return response()->json([
            'message' => 'Travel request submitted successfully.',
            'data' => $this->serializeTravelRequest($travelRequest),
        ], 201);
    }

    public function show(Request $request, TravelRequest $travelRequest): JsonResponse
    {
        $user = $this->authenticatedUser($request);

        if ($user instanceof JsonResponse) {
            return $user;
        }

        if (! $this->canView($user, $travelRequest)) {
            return response()->json(['message' => 'Unauthorized.'], 403);
        }

        $travelRequest->load(['user.roles', 'project']);

        return response()->json([
            'data' => $this->serializeTravelRequest($travelRequest),
        ]);
    }

    public function approve(Request $request, TravelRequest $travelRequest): JsonResponse
    {
        $user = $this->authenticatedUser($request);

        if ($user instanceof JsonResponse) {
            return $user;
        }

        if ($user->hasRole('reception')) {
            return response()->json(['message' => 'Unauthorized.'], 403);
        }

        $pmProjectId = $user->hasRole('project-manager') ? $user->approverProjectId() : null;

        if ($user->hasRole('project-manager') && $travelRequest->status === 'pending_pm' && $pmProjectId && (int) $travelRequest->project_id === (int) $pmProjectId) {
            $travelRequest->update([
                'status' => 'pending_commercial',
                'pm_id' => $user->id,
                'pm_approved_at' => now(),
            ]);

            if ($travelRequest->user) {
                $travelRequest->user->notify(new TicketStatusUpdated($travelRequest, 'Your ticket for ' . $travelRequest->destination . ' was approved by your PM and sent to the Director.', 'info'));
            }

            $this->notifyCommercialDirectors(
                $travelRequest,
                'New ticket for ' . $travelRequest->destination . ' was approved by PM and awaits your approval.',
                'warning'
            );

            return response()->json([
                'message' => 'Approved by PM, forwarded to Commercial Director.',
                'data' => $this->serializeTravelRequest($travelRequest->fresh(['user.roles', 'project'])),
            ]);
        }

        if ($user->hasAnyRole(['commercial-director', 'admin']) && in_array($travelRequest->status, ['pending_commercial', 'pending_hod'], true)) {
            $nextStatus = $travelRequest->flight_type === 'international' ? 'pending_ceo' : 'approved';

            $travelRequest->update([
                'status' => $nextStatus,
                'hod_id' => $user->id,
                'hod_approved_at' => now(),
            ]);

            if ($nextStatus === 'pending_ceo') {
                $ceos = User::role('ceo')->get();
                foreach ($ceos as $ceo) {
                    $ceo->notify(new TicketStatusUpdated($travelRequest, 'International ticket for ' . $travelRequest->destination . ' needs CEO approval.', 'warning'));
                }

                return response()->json([
                    'message' => 'Approved by Commercial Director. Forwarded to CEO.',
                    'data' => $this->serializeTravelRequest($travelRequest->fresh(['user.roles', 'project'])),
                ]);
            }

            if ($travelRequest->user) {
                $travelRequest->user->notify(new TicketStatusUpdated($travelRequest, 'Your ticket for ' . $travelRequest->destination . ' has been FINALLY APPROVED.', 'success'));
            }

            foreach (User::role('reception')->get() as $reception) {
                $reception->notify(new TicketStatusUpdated($travelRequest, 'A ticket for ' . $travelRequest->destination . ' has been approved and is ready for processing.', 'info'));
            }

            return response()->json([
                'message' => 'Approved by Commercial Director. Request is fully approved.',
                'data' => $this->serializeTravelRequest($travelRequest->fresh(['user.roles', 'project'])),
            ]);
        }

        if ($user->hasRole('ceo') && $travelRequest->status === 'pending_ceo') {
            $travelRequest->update(['status' => 'approved']);

            if ($travelRequest->user) {
                $travelRequest->user->notify(new TicketStatusUpdated($travelRequest, 'Your international ticket for ' . $travelRequest->destination . ' has been APPROVED by the CEO.', 'success'));
            }

            foreach (User::role('reception')->get() as $reception) {
                $reception->notify(new TicketStatusUpdated($travelRequest, 'An international ticket for ' . $travelRequest->destination . ' has been approved and is ready for processing.', 'info'));
            }

            return response()->json([
                'message' => 'Approved by CEO. Request is fully approved.',
                'data' => $this->serializeTravelRequest($travelRequest->fresh(['user.roles', 'project'])),
            ]);
        }

        return response()->json(['message' => 'Unauthorized action or invalid status.'], 403);
    }

    public function reject(Request $request, TravelRequest $travelRequest): JsonResponse
    {
        $user = $this->authenticatedUser($request);

        if ($user instanceof JsonResponse) {
            return $user;
        }

        if ($user->hasRole('reception')) {
            return response()->json(['message' => 'Unauthorized.'], 403);
        }

        $reason = $request->validate([
            'rejection_reason' => ['required', 'string', 'max:1000'],
        ])['rejection_reason'];

        $pmProjectId = $user->hasRole('project-manager') ? $user->approverProjectId() : null;

        if ($user->hasRole('project-manager') && $travelRequest->status === 'pending_pm' && $pmProjectId && (int) $travelRequest->project_id === (int) $pmProjectId) {
            $travelRequest->update([
                'status' => 'rejected',
                'pm_id' => $user->id,
                'rejection_reason' => $reason,
            ]);

            if ($travelRequest->user) {
                $travelRequest->user->notify(new TicketStatusUpdated($travelRequest, 'Your ticket for ' . $travelRequest->destination . ' was REJECTED by your PM. Reason: ' . $reason, 'error'));
            }

            return response()->json([
                'message' => 'Request rejected by PM.',
                'data' => $this->serializeTravelRequest($travelRequest->fresh(['user.roles', 'project'])),
            ]);
        }

        if ($user->hasAnyRole(['commercial-director', 'admin']) && in_array($travelRequest->status, ['pending_commercial', 'pending_hod'], true)) {
            $travelRequest->update([
                'status' => 'rejected',
                'hod_id' => $user->id,
                'rejection_reason' => $reason,
            ]);

            if ($travelRequest->user) {
                $travelRequest->user->notify(new TicketStatusUpdated($travelRequest, 'Your ticket for ' . $travelRequest->destination . ' was REJECTED by the Director. Reason: ' . $reason, 'error'));
            }

            return response()->json([
                'message' => 'Request rejected by Commercial Director.',
                'data' => $this->serializeTravelRequest($travelRequest->fresh(['user.roles', 'project'])),
            ]);
        }

        if ($user->hasRole('ceo') && $travelRequest->status === 'pending_ceo') {
            $travelRequest->update([
                'status' => 'rejected',
                'rejection_reason' => $reason,
            ]);

            if ($travelRequest->user) {
                $travelRequest->user->notify(new TicketStatusUpdated($travelRequest, 'Your international ticket for ' . $travelRequest->destination . ' was REJECTED by the CEO. Reason: ' . $reason, 'error'));
            }

            return response()->json([
                'message' => 'Request rejected by CEO.',
                'data' => $this->serializeTravelRequest($travelRequest->fresh(['user.roles', 'project'])),
            ]);
        }

        return response()->json(['message' => 'Unauthorized action or invalid status.'], 403);
    }

    private function canView(User $user, TravelRequest $travelRequest): bool
    {
        if ($user->hasAnyRole(['admin', 'ceo', 'head-office-director', 'commercial-director'])) {
            return true;
        }

        if ($user->hasRole('project-manager')) {
            $pmProjectId = $user->approverProjectId();

            return (int) $travelRequest->user_id === (int) $user->id
                || ($pmProjectId && (int) $travelRequest->project_id === (int) $pmProjectId);
        }

        return (int) $travelRequest->user_id === (int) $user->id;
    }

    private function sendCreatedNotifications(TravelRequest $travelRequest, User $user): void
    {
        if ($travelRequest->status === 'approved') {
            $pm = $travelRequest->project?->resolveManager();
            if ($pm) {
                $pm->notify(new TicketStatusUpdated(
                    $travelRequest,
                    'CEO requested a ticket for ' . $travelRequest->destination . '. It is already approved (info only).',
                    'info'
                ));
            }

            foreach (User::role('commercial-director')->get() as $director) {
                $director->notify(new TicketStatusUpdated(
                    $travelRequest,
                    'CEO requested a ticket for ' . $travelRequest->destination . '. It is already approved (info only).',
                    'info'
                ));
            }

            foreach (User::role('reception')->get() as $reception) {
                $reception->notify(new TicketStatusUpdated(
                    $travelRequest,
                    'CEO ticket for ' . $travelRequest->destination . ' is approved and ready for processing.',
                    'info'
                ));
            }

            return;
        }

        if ($travelRequest->status === 'pending_pm') {
            $this->notifyProjectManager(
                $travelRequest,
                $user->name . ' submitted a new ticket for ' . $travelRequest->destination . ' awaiting your approval.',
                'warning'
            );
        } elseif ($travelRequest->status === 'pending_commercial') {
            $this->notifyCommercialDirectors(
                $travelRequest,
                'New ticket for ' . $travelRequest->destination . ' from PM ' . $user->name . ' awaits your approval.',
                'warning'
            );
        }
    }

    private function notifyCommercialDirectors(TravelRequest $travelRequest, string $message, string $type): void
    {
        $directors = User::role('commercial-director')->get();

        foreach ($directors as $director) {
            $director->notify(new TicketStatusUpdated($travelRequest, $message, $type));
        }

        if ($directors->isEmpty()) {
            $fallbackEmail = config('travel.commercial_director_email');
            if ($fallbackEmail) {
                Notification::route('mail', $fallbackEmail)
                    ->notify(new TicketStatusUpdated($travelRequest, $message, $type));
            }
        }
    }

    private function notifyProjectManager(TravelRequest $travelRequest, string $message, string $type): void
    {
        $pm = $travelRequest->project?->resolveManager();

        if ($pm) {
            $pm->notify(new TicketStatusUpdated($travelRequest, $message, $type));
        }
    }

    private function serializeProject(Project $project): array
    {
        return [
            'id' => $project->id,
            'name' => $project->name,
            'project_code' => $project->project_code,
            'location' => $project->location,
            'status' => $project->status,
        ];
    }

    private function serializeTravelRequest(TravelRequest $travelRequest): array
    {
        return [
            'id' => $travelRequest->id,
            'origin' => $travelRequest->origin,
            'destination' => $travelRequest->destination,
            'passenger_count' => $travelRequest->passenger_count,
            'additional_passengers' => $travelRequest->additionalPassengerNames(),
            'all_passenger_names' => $travelRequest->allPassengerNames(),
            'flight_type' => $travelRequest->flight_type,
            'travel_date' => $this->serializeDate($travelRequest->travel_date),
            'return_date' => $this->serializeDate($travelRequest->return_date),
            'purpose' => $travelRequest->purpose,
            'remarks' => $travelRequest->remarks,
            'status' => $travelRequest->status,
            'project' => $travelRequest->project ? $this->serializeProject($travelRequest->project) : null,
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
