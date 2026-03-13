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
                    $q->whereIn('status', ['pending_commercial', 'pending_hod', 'approved', 'rejected'])
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

        $travelRequests = $query->latest()->paginate(10);
        return view('travel_requests.index', compact('travelRequests'));
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
        $allowedProjectId = $user->hasRole('project-manager')
            ? ($user->managedProject?->id ?? $user->project_id)
            : $user->project_id;

        // Restricted users (regular + PM) can only raise for their assigned/managed project.
        if (!$isPrivileged) {
            if (!$allowedProjectId) {
                return redirect()->route('travel-requests.index')
                    ->with('error', 'You are not assigned to a project yet. Please contact an admin to add you to a project.');
            }

            if ($project_id && (int) $project_id !== (int) $allowedProjectId) {
                abort(403, 'Unauthorized project selection.');
            }

            $preselectedProject = Project::findOrFail($allowedProjectId);
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
            'travel_date' => 'required|date',
            'return_date' => 'nullable|date|after_or_equal:travel_date',
            'purpose' => 'required|string',
            'remarks' => 'nullable|string',
        ]);

        $user = Auth::user();
        $isPrivileged = $user->hasAnyRole(['admin', 'head-office-director', 'commercial-director', 'ceo']);
        $allowedProjectId = $user->hasRole('project-manager')
            ? ($user->managedProject?->id ?? $user->project_id)
            : $user->project_id;

        if (!$isPrivileged) {
            if (!$allowedProjectId) {
                return back()->withErrors([
                    'project_id' => 'You are not assigned to a project yet. Please contact an admin.',
                ])->withInput();
            }

            if ((int) $validated['project_id'] !== (int) $allowedProjectId) {
                return back()->withErrors([
                    'project_id' => 'You can only request travel for your assigned project.',
                ])->withInput();
            }
        }

        $travelRequest = new TravelRequest($validated);
        $travelRequest->user_id = Auth::id();
        $travelRequest->project_id = $request->project_id;
        $travelRequest->origin = $validated['origin'];
        $travelRequest->passenger_count = $validated['passenger_count'];
        // If the requester is a project manager, we skip the PM approval stage.
        if (Auth::user()->hasRole('project-manager')) {
            $travelRequest->status = 'pending_commercial';
        } else {
            $travelRequest->status = 'pending_pm';
        }
        $travelRequest->save();
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

            // Notify Directors
            $directors = User::role(['commercial-director', 'head-office-director'])->get();
            foreach ($directors as $director) {
                $director->notify(new TicketStatusUpdated($travelRequest, 'New ticket for ' . $travelRequest->destination . ' awaits your approval.', 'warning'));
            }

            return redirect()->back()->with('success', 'Approved by PM, forwarded to Commercial Director.');
        }

        // Commercial Director (or Admin) approval
        if ($user->hasRole('commercial-director') && in_array($travelRequest->status, ['pending_commercial', 'pending_hod'], true)) {
            $travelRequest->update([
                'status' => 'approved',
                // reusing hod_id or should we add commercial_id? We can reuse hod_id or just leave it out for now since the column is hod_id.
                // Or if we run a migration we could add it. For now, reusing hod_id or skipping tracking who approved it.
                'hod_id' => $user->id,
            ]);

            // Notify Requester
            if ($travelRequest->user) {
                $travelRequest->user->notify(new TicketStatusUpdated($travelRequest, 'Your ticket for ' . $travelRequest->destination . ' has been FINALLY APPROVED.', 'success'));
            }

            return redirect()->back()->with('success', 'Approved by Commercial Director. Request is fully approved.');
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

        abort(403, 'Unauthorized action or invalid status.');
    }
}
