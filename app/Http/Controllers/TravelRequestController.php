<?php

namespace App\Http\Controllers;

use App\Models\TravelRequest;
use App\Models\Project;
use App\Models\User;
use App\Notifications\TicketStatusUpdated;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TravelRequestController extends Controller
{
    private function notifyCommercialDirectors(TravelRequest $travelRequest, string $message, string $type = 'warning'): void
    {
        $directors = User::role('commercial-director')->get();
        foreach ($directors as $director) {
            $director->notify(new TicketStatusUpdated($travelRequest, $message, $type));
        }
    }

    private function notifyProjectManager(TravelRequest $travelRequest, string $message, string $type = 'warning'): void
    {
        $travelRequest->loadMissing('project.manager', 'user');
        $pm = $travelRequest->project?->manager;

        if ($pm) {
            $pm->notify(new TicketStatusUpdated($travelRequest, $message, $type));
        }
    }

    private function requestableProjectsFor(User $user)
    {
        $projectIds = collect([$user->project_id]);

        if ($user->hasRole('project-manager') && $user->managedProject) {
            $projectIds->push($user->managedProject->id);
        }

        $projectIds = $projectIds
            ->merge($user->projects()->pluck('projects.id'))
            ->filter()
            ->unique()
            ->values();

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

        if ($user->hasRole('admin') || $user->hasRole('ceo') || $user->hasRole('head-office-director')) {
            // Can see all (ceo is view only, admin/hod have actions if needed, though hod is deprecated in new flow)
        } elseif ($user->hasRole('commercial-director')) {
            if ($viewType === 'personal') {
                $query->where('user_id', $user->id);
            } else {
                // Can see all requests that are at the commercial-director stage, or already approved/rejected,
                // plus any they created themselves.
                $query->where(function ($q) use ($user) {
                    $q->whereIn('status', ['pending_commercial', 'pending_hod', 'pending_ceo', 'approved', 'rejected'])
                        ->orWhere('user_id', $user->id);
                });
            }
        } elseif ($user->hasRole('project-manager')) {
            if ($viewType === 'personal') {
                $query->where('user_id', $user->id);
            } else {
                // PMs see everything in their project
                $pmProjectId = $user->managedProject?->id ?? $user->project_id;
                $query->where('project_id', $pmProjectId ?? -1);
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
            $query->where('status', $status);
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

            return view('travel_requests.create', compact('preselectedProject', 'projects'));
        }

        // Privileged roles can select any project (or use a preselected one)
        if ($project_id) {
            $preselectedProject = Project::findOrFail($project_id);
        } else {
            $projects = Project::orderBy('name')->get();
        }

        return view('travel_requests.create', compact('preselectedProject', 'projects'));
    }

    public function store(Request $request)
    {
        if (Auth::user()->hasRole('reception')) {
            abort(403);
        }
        $validated = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'destination' => 'required|string|max:255',
            'origin' => 'required|string|max:255',
            'passenger_count' => 'required|integer|min:1',
            'flight_type' => 'required|in:national,international',
            'travel_date' => 'required|date',
            'return_date' => 'nullable|date|after_or_equal:travel_date',
            'purpose' => 'required|string',
            'remarks' => 'nullable|string',
        ]);

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
            $pm = $travelRequest->project?->manager;
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
        $pmProjectId = $user->hasRole('project-manager') ? ($user->managedProject?->id ?? $user->project_id) : null;

        if (
            !$user->hasRole('admin') && !$user->hasRole('ceo') && !$user->hasRole('commercial-director') && !$user->hasRole('head-office-director') &&
            !($user->hasRole('project-manager') && $pmProjectId && (int) $travelRequest->project_id === (int) $pmProjectId) &&
            $travelRequest->user_id !== $user->id
        ) {
            abort(403);
        }

        return view('travel_requests.show', compact('travelRequest'));
    }

    public function edit(TravelRequest $travelRequest)
    {
        if (Auth::user()->hasRole('reception')) {
            abort(403);
        }
        if ($travelRequest->user_id !== Auth::id() || ($travelRequest->status !== 'pending_pm' && $travelRequest->status !== 'pending_commercial')) {
            abort(403, 'Cannot edit this request at this stage.');
        }

        // Only allow edit before the first approval stage:
        // - Regular users: only while pending_pm
        // - PM requester (skips PM stage): allow while pending_commercial
        if ($travelRequest->status === 'pending_commercial' && !Auth::user()->hasRole('project-manager')) {
            abort(403, 'Cannot edit this request after PM approval.');
        }

        return view('travel_requests.edit', compact('travelRequest'));
    }

    public function update(Request $request, TravelRequest $travelRequest)
    {
        if (Auth::user()->hasRole('reception')) {
            abort(403);
        }
        if ($travelRequest->user_id !== Auth::id() || ($travelRequest->status !== 'pending_pm' && $travelRequest->status !== 'pending_commercial')) {
            abort(403, 'Cannot edit this request at this stage.');
        }

        if ($travelRequest->status === 'pending_commercial' && !Auth::user()->hasRole('project-manager')) {
            abort(403, 'Cannot edit this request after PM approval.');
        }

        $validated = $request->validate([
            'destination' => 'required|string|max:255',
            'origin' => 'required|string|max:255',
            'passenger_count' => 'required|integer|min:1',
            'flight_type' => 'required|in:national,international',
            'travel_date' => 'required|date',
            'return_date' => 'nullable|date|after_or_equal:travel_date',
            'purpose' => 'required|string',
            'remarks' => 'nullable|string',
        ]);
        $travelRequest->update($validated);

        return redirect()->route('travel-requests.index')->with('success', 'Travel request updated successfully.');
    }

    public function destroy(TravelRequest $travelRequest)
    {
        if (Auth::user()->hasRole('reception')) {
            abort(403);
        }
        if ($travelRequest->user_id !== Auth::id() || ($travelRequest->status !== 'pending_pm' && $travelRequest->status !== 'pending_commercial')) {
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
        $pmProjectId = $user->hasRole('project-manager') ? ($user->managedProject?->id ?? $user->project_id) : null;

        // PM approval
        if ($user->hasRole('project-manager') && $travelRequest->status === 'pending_pm' && $pmProjectId && (int) $travelRequest->project_id === (int) $pmProjectId) {
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
        $pmProjectId = $user->hasRole('project-manager') ? ($user->managedProject?->id ?? $user->project_id) : null;

        $reason = $request->validate([
            'rejection_reason' => 'required|string|max:1000',
        ])['rejection_reason'];

        // PM Rejection
        if ($user->hasRole('project-manager') && $travelRequest->status === 'pending_pm' && $pmProjectId && (int) $travelRequest->project_id === (int) $pmProjectId) {
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
}
