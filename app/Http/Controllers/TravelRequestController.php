<?php

namespace App\Http\Controllers;

use App\Models\TravelRequest;
use App\Models\Project;
use App\Models\User;
use App\Models\City;
use App\Notifications\TicketStatusUpdated;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;
use Illuminate\Validation\Rule;

class TravelRequestController extends Controller
{
    private function notifyCommercialDirectors(TravelRequest $travelRequest, string $message, string $type = 'warning'): void
    {
        $travelRequest->loadMissing('project', 'user');
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

    private function notifyProjectManager(TravelRequest $travelRequest, string $message, string $type = 'warning'): void
    {
        $travelRequest->loadMissing('project', 'user');
        $pm = $travelRequest->project?->resolveManager();

        if ($pm) {
            $pm->notify(new TicketStatusUpdated($travelRequest, $message, $type));
        }
    }

    private function citiesForSelect(?string ...$includeNames)
    {
        $cities = City::active()->ordered()->get();

        foreach (array_filter($includeNames) as $name) {
            if (!$cities->contains('name', $name)) {
                $cities->push(new City(['name' => $name, 'region' => null, 'is_active' => false]));
            }
        }

        return $cities->sortBy('name')->values();
    }

    private function requestableProjectsFor(User $user)
    {
        $projectIds = $user->memberProjectIds();

        if ($projectIds->isEmpty()) {
            return collect();
        }

        return Project::whereIn('id', $projectIds)->orderBy('name')->get();
    }

    public function index(Request $request)
    {
        $user = Auth::user();
        if ($user->hasRole('reception')) {
            return redirect()->route('reception.tickets.index');
        }
        $query = TravelRequest::with(['user', 'project']);
        $viewType = $request->query('view');

        if ($user->hasRole('commercial-director')) {
            if ($viewType === 'personal') {
                $query->where('user_id', $user->id);
            } elseif ($viewType === 'all') {
                // Total card: every request in the system (no default filter).
            } elseif (($viewType === 'approved' || $viewType === null) && ! $request->filled('status')) {
                // Default Travel Requests tab and "approved" history: only requests approved by the PM
                // (i.e. awaiting the Commercial Director's decision). Explicit ?status= filters take precedence.
                $query->where('status', 'pending_commercial');
            }
        } elseif ($user->hasAnyRole(['admin', 'ceo', 'head-office-director'])) {
            // Unrestricted roles see everything (filters below still apply).
        } elseif ($user->hasRole('project-manager')) {
            if ($viewType === 'personal') {
                $query->where('user_id', $user->id);
            } else {
                // PMs see requests from every project they manage
                $pmProjectIds = $user->approverProjectIds();
                $query->whereIn('project_id', $pmProjectIds->isEmpty() ? [-1] : $pmProjectIds);
            }
        } else {
            // Regular users see only their own
            $query->where('user_id', $user->id);
        }

        // Filters
        $status = $request->query('status');
        $flightType = $request->query('flight_type');
        $projectId = $request->query('project_id');
        $dateFrom = $request->query('date_from');
        $dateTo = $request->query('date_to');
        $keyword = trim((string) $request->query('keyword', ''));

        if ($status) {
            if ($status === 'pending') {
                // Handle "pending" as a special filter for all pending statuses
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

        if ($projectId) {
            $query->where('project_id', $projectId);
        }

        if ($flightType) {
            $query->where('flight_type', $flightType);
        }

        if ($dateFrom) {
            $query->whereDate('travel_date', '>=', $dateFrom);
        }

        if ($dateTo) {
            $query->whereDate('travel_date', '<=', $dateTo);
        }

        if ($keyword !== '') {
            $query->where(function ($q) use ($keyword) {
                $q->where('destination', 'like', '%' . $keyword . '%')
                    ->orWhere('origin', 'like', '%' . $keyword . '%')
                    ->orWhere('purpose', 'like', '%' . $keyword . '%')
                    ->orWhere('remarks', 'like', '%' . $keyword . '%')
                    ->orWhereHas('user', function ($u) use ($keyword) {
                        $u->where('name', 'like', '%' . $keyword . '%')
                            ->orWhere('email', 'like', '%' . $keyword . '%');
                    })
                    ->orWhereHas('project', function ($p) use ($keyword) {
                        $p->where('name', 'like', '%' . $keyword . '%');
                    });
            });
        }

        $projects = collect();
        if ($user->hasAnyRole(['admin', 'ceo', 'head-office-director', 'commercial-director'])) {
            $projects = Project::orderBy('name')->get();
        } else {
            $projects = $this->requestableProjectsFor($user);
        }

        $filters = [
            'status' => $status,
            'project_id' => $projectId,
            'flight_type' => $flightType,
            'date_from' => $dateFrom,
            'date_to' => $dateTo,
            'keyword' => $keyword,
            'view' => $viewType,
        ];

        $travelRequests = $query->latest()->paginate(10)->withQueryString();
        return view('travel_requests.index', compact('travelRequests', 'projects', 'filters'));
    }

    public function create(Request $request)
    {
        if (Auth::user()->hasRole('reception')) {
            abort(403);
        }
        $user = Auth::user();
        $project_id = $request->query('project_id');
        $preselectedProject = null;
        $projects = collect();

        $isPrivileged = $user->hasAnyRole(['admin', 'head-office-director', 'commercial-director', 'ceo']);

        // Restricted users (regular + PM) can only raise for projects they belong to.
        if (!$isPrivileged) {
            $allowedProjects = $this->requestableProjectsFor($user);

            if ($allowedProjects->isEmpty()) {
                return redirect()->route('travel-requests.index')
                    ->with('error', 'You are not assigned to a project yet. Please contact an admin to add you to a project.');
            }

            if ($project_id && !$allowedProjects->contains('id', (int) $project_id)) {
                abort(403, 'Unauthorized project selection.');
            }

            if ($allowedProjects->count() === 1) {
                $preselectedProject = $allowedProjects->first();
            } else {
                $projects = $allowedProjects;
            }

            $cities = $this->citiesForSelect(old('origin'), old('destination'));

            return view('travel_requests.create', compact('preselectedProject', 'projects', 'cities'));
        }

        // Privileged roles can select any project (or use a preselected one)
        if ($project_id) {
            $preselectedProject = Project::findOrFail($project_id);
        } else {
            $projects = Project::orderBy('name')->get();
        }

        $cities = $this->citiesForSelect(old('origin'), old('destination'));

        return view('travel_requests.create', compact('preselectedProject', 'projects', 'cities'));
    }

    public function store(Request $request)
    {
        if (Auth::user()->hasRole('reception')) {
            abort(403);
        }
        $validated = $this->validateTravelRequest($request);

        $user = Auth::user();
        $isPrivileged = $user->hasAnyRole(['admin', 'head-office-director', 'commercial-director', 'ceo']);

        if (!$isPrivileged) {
            $allowedProjectIds = $this->requestableProjectsFor($user)->pluck('id');

            if ($allowedProjectIds->isEmpty()) {
                return back()->withErrors([
                    'project_id' => 'You are not assigned to a project yet. Please contact an admin.',
                ])->withInput();
            }

            if (!$allowedProjectIds->contains((int) $validated['project_id'])) {
                return back()->withErrors([
                    'project_id' => 'You can only request travel for a project you are a member of.',
                ])->withInput();
            }
        }

        $travelRequest = new TravelRequest($validated);
        $travelRequest->user_id = Auth::id();
        $travelRequest->project_id = $request->project_id;
        $travelRequest->origin = $validated['origin'];
        $travelRequest->passenger_count = $validated['passenger_count'];
        $travelRequest->flight_type = $validated['flight_type'];
        // If the requester is CEO, auto-approve and notify PM/Commercial as info + Reception to process.
        if ($user->hasRole('ceo')) {
            $travelRequest->status = 'approved';
            $travelRequest->save();

            $travelRequest->load('project');
            $pm = $travelRequest->project?->resolveManager();
            if ($pm) {
                $pm->notify(new TicketStatusUpdated(
                    $travelRequest,
                    'CEO requested a ticket for ' . $travelRequest->destination . '. It is already approved (info only).',
                    'info'
                ));
            }

            $commercialManagers = User::role('commercial-director')->get();
            foreach ($commercialManagers as $director) {
                $director->notify(new TicketStatusUpdated(
                    $travelRequest,
                    'CEO requested a ticket for ' . $travelRequest->destination . '. It is already approved (info only).',
                    'info'
                ));
            }

            $receptions = User::role('reception')->get();
            foreach ($receptions as $reception) {
                $reception->notify(new TicketStatusUpdated(
                    $travelRequest,
                    'CEO ticket for ' . $travelRequest->destination . ' is approved and ready for processing.',
                    'info'
                ));
            }

            return redirect()->route('travel-requests.index')->with('success', 'CEO request auto-approved and sent to Reception.');
        }

        // If the requester is a project manager, we skip the PM approval stage.
        if ($user->hasRole('project-manager')) {
            $travelRequest->status = 'pending_commercial';
        } else {
            $travelRequest->status = 'pending_pm';
        }
        $travelRequest->save();
        $travelRequest->load('project');

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

        return redirect()->route('travel-requests.index')->with('success', 'Travel request submitted successfully.');
    }

    public function show(TravelRequest $travelRequest)
    {
        if (Auth::user()->hasRole('reception')) {
            abort(403);
        }
        $user = Auth::user();
        $pmProjectIds = $user->hasRole('project-manager') ? $user->approverProjectIds() : collect();

        if (
            ! $user->hasRole('admin') && ! $user->hasRole('ceo') && ! $user->hasRole('commercial-director') && ! $user->hasRole('head-office-director') &&
            ! ($user->hasRole('project-manager') && $pmProjectIds->contains((int) $travelRequest->project_id)) &&
            $travelRequest->user_id != $user->id
        ) {
            abort(403);
        }

        $travelRequest->load(['user.roles', 'project', 'pm', 'hod']);

        return view('travel_requests.show', compact('travelRequest'));
    }

    public function edit(TravelRequest $travelRequest)
    {
        if (Auth::user()->hasRole('reception')) {
            abort(403);
        }
        if ($travelRequest->user_id != Auth::id() || ($travelRequest->status !== 'pending_pm' && $travelRequest->status !== 'pending_commercial')) {
            abort(403, 'Cannot edit this request at this stage.');
        }

        // Only allow edit before the first approval stage:
        // - Regular users: only while pending_pm
        // - PM requester (skips PM stage): allow while pending_commercial
        if ($travelRequest->status === 'pending_commercial' && !Auth::user()->hasRole('project-manager')) {
            abort(403, 'Cannot edit this request after PM approval.');
        }

        $cities = $this->citiesForSelect(
            old('origin', $travelRequest->origin),
            old('destination', $travelRequest->destination)
        );

        return view('travel_requests.edit', compact('travelRequest', 'cities'));
    }

    public function update(Request $request, TravelRequest $travelRequest)
    {
        if (Auth::user()->hasRole('reception')) {
            abort(403);
        }
        if ($travelRequest->user_id != Auth::id() || ($travelRequest->status !== 'pending_pm' && $travelRequest->status !== 'pending_commercial')) {
            abort(403, 'Cannot edit this request at this stage.');
        }

        if ($travelRequest->status === 'pending_commercial' && !Auth::user()->hasRole('project-manager')) {
            abort(403, 'Cannot edit this request after PM approval.');
        }

        $validated = $this->validateTravelRequest($request, includeProject: false);
        $travelRequest->update($validated);

        return redirect()->route('travel-requests.index')->with('success', 'Travel request updated successfully.');
    }

    public function destroy(TravelRequest $travelRequest)
    {
        if (Auth::user()->hasRole('reception')) {
            abort(403);
        }
        if ($travelRequest->user_id != Auth::id() || ($travelRequest->status !== 'pending_pm' && $travelRequest->status !== 'pending_commercial')) {
            abort(403, 'Cannot delete this request at this stage.');
        }

        if ($travelRequest->status === 'pending_commercial' && !Auth::user()->hasRole('project-manager')) {
            abort(403, 'Cannot delete this request after PM approval.');
        }

        $travelRequest->delete();

        return redirect()->route('travel-requests.index')->with('success', 'Travel request deleted successfully.');
    }

    public function approve(Request $request, TravelRequest $travelRequest)
    {
        if (Auth::user()->hasRole('reception')) {
            abort(403);
        }
        $user = Auth::user();
        $pmProjectIds = $user->hasRole('project-manager') ? $user->approverProjectIds() : collect();

        // PM approval — the PM may approve requests of any project they manage (projects.manager_id)
        if ($user->hasRole('project-manager') && $travelRequest->status === 'pending_pm' && $pmProjectIds->contains((int) $travelRequest->project_id)) {
            $travelRequest->update([
                'status' => 'pending_commercial',
                'pm_id' => $user->id,
                'pm_approved_at' => now(),
            ]);

            // Notify Requester
            if ($travelRequest->user) {
                $travelRequest->user->notify(new TicketStatusUpdated($travelRequest, 'Your ticket for ' . $travelRequest->destination . ' was approved by your PM and sent to the Director.', 'info'));
            }

            // Notify Commercial Directors
            $this->notifyCommercialDirectors(
                $travelRequest,
                'New ticket for ' . $travelRequest->destination . ' was approved by PM and awaits your approval.',
                'warning'
            );

            return redirect()->back()->with('success', 'Approved by PM, forwarded to Commercial Director.');
        }

        // Commercial Director (or Admin) approval
        if ($user->hasRole('commercial-director') && in_array($travelRequest->status, ['pending_commercial', 'pending_hod'], true)) {
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
                return redirect()->back()->with('success', 'Approved by Commercial Director. Forwarded to CEO.');
            }

            // Notify Requester
            if ($travelRequest->user) {
                $travelRequest->user->notify(new TicketStatusUpdated($travelRequest, 'Your ticket for ' . $travelRequest->destination . ' has been FINALLY APPROVED.', 'success'));
            }

            // Notify Reception users
            $receptions = \App\Models\User::role('reception')->get();
            foreach ($receptions as $reception) {
                $reception->notify(new TicketStatusUpdated($travelRequest, 'A ticket for ' . $travelRequest->destination . ' has been approved and is ready for processing.', 'info'));
            }

            return redirect()->back()->with('success', 'Approved by Commercial Director. Request is fully approved.');
        }

        // CEO approval (International only)
        if ($user->hasRole('ceo') && $travelRequest->status === 'pending_ceo') {
            $travelRequest->update([
                'status' => 'approved',
            ]);

            // Notify Requester
            if ($travelRequest->user) {
                $travelRequest->user->notify(new TicketStatusUpdated($travelRequest, 'Your international ticket for ' . $travelRequest->destination . ' has been APPROVED by the CEO.', 'success'));
            }

            // Notify Reception users
            $receptions = \App\Models\User::role('reception')->get();
            foreach ($receptions as $reception) {
                $reception->notify(new TicketStatusUpdated($travelRequest, 'An international ticket for ' . $travelRequest->destination . ' has been approved and is ready for processing.', 'info'));
            }

            return redirect()->back()->with('success', 'Approved by CEO. Request is fully approved.');
        }

        abort(403, 'Unauthorized action or invalid status.');
    }

    public function reject(Request $request, TravelRequest $travelRequest)
    {
        if (Auth::user()->hasRole('reception')) {
            abort(403);
        }
        $user = Auth::user();
        $pmProjectIds = $user->hasRole('project-manager') ? $user->approverProjectIds() : collect();

        $reason = $request->validate([
            'rejection_reason' => 'required|string|max:1000',
        ])['rejection_reason'];

        // PM Rejection — the PM may reject requests of any project they manage
        if ($user->hasRole('project-manager') && $travelRequest->status === 'pending_pm' && $pmProjectIds->contains((int) $travelRequest->project_id)) {
            $travelRequest->update([
                'status' => 'rejected',
                'pm_id' => $user->id,
                'rejection_reason' => $reason,
            ]);

            if ($travelRequest->user) {
                $travelRequest->user->notify(new TicketStatusUpdated($travelRequest, 'Your ticket for ' . $travelRequest->destination . ' was REJECTED by your PM. Reason: ' . $reason, 'error'));
            }

            return redirect()->back()->with('success', 'Request rejected by PM.');
        }

        // Commercial Director Rejection
        if ($user->hasRole('commercial-director') && in_array($travelRequest->status, ['pending_commercial', 'pending_hod'], true)) {
            $travelRequest->update([
                'status' => 'rejected',
                'hod_id' => $user->id,
                'rejection_reason' => $reason,
            ]);

            if ($travelRequest->user) {
                $travelRequest->user->notify(new TicketStatusUpdated($travelRequest, 'Your ticket for ' . $travelRequest->destination . ' was REJECTED by the Director. Reason: ' . $reason, 'error'));
            }

            return redirect()->back()->with('success', 'Request rejected by Commercial Director.');
        }

        // CEO Rejection (International only)
        if ($user->hasRole('ceo') && $travelRequest->status === 'pending_ceo') {
            $travelRequest->update([
                'status' => 'rejected',
                'rejection_reason' => $reason,
            ]);

            if ($travelRequest->user) {
                $travelRequest->user->notify(new TicketStatusUpdated($travelRequest, 'Your international ticket for ' . $travelRequest->destination . ' was REJECTED by the CEO. Reason: ' . $reason, 'error'));
            }

            return redirect()->back()->with('success', 'Request rejected by CEO.');
        }

        abort(403, 'Unauthorized action or invalid status.');
    }

    /**
     * @return array<string, mixed>
     */
    private function validateTravelRequest(Request $request, bool $includeProject = true): array
    {
        $rules = [
            'destination' => ['required', 'string', 'max:255', Rule::exists('cities', 'name')->where('is_active', true)],
            'origin' => ['required', 'string', 'max:255', Rule::exists('cities', 'name')->where('is_active', true)],
            'passenger_count' => 'required|integer|min:1|max:50',
            'flight_type' => 'required|in:national,international',
            'travel_date' => 'required|date',
            'return_date' => 'nullable|date|after_or_equal:travel_date',
            'purpose' => 'required|string',
            'remarks' => 'nullable|string',
        ];

        if ($includeProject) {
            $rules['project_id'] = 'required|exists:projects,id';
        }

        $validated = $request->validate($rules);

        $passengerCount = (int) $validated['passenger_count'];
        $additionalCount = max(0, $passengerCount - 1);

        if ($additionalCount > 0) {
            $request->validate([
                'additional_passengers' => ['required', 'array', 'size:'.$additionalCount],
                'additional_passengers.*' => ['required', 'string', 'max:255'],
            ], [
                'additional_passengers.required' => 'Please enter the full name for each additional passenger.',
                'additional_passengers.size' => 'Please enter exactly '.$additionalCount.' additional passenger name(s).',
                'additional_passengers.*.required' => 'Each additional passenger must have a full name (including grandfather).',
            ]);

            $validated['additional_passengers'] = array_values(
                array_map('trim', $request->input('additional_passengers', []))
            );
        } else {
            $validated['additional_passengers'] = null;
        }

        return $validated;
    }
}
