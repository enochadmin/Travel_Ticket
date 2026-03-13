<?php

namespace App\Http\Controllers;

use App\Models\ReceptionTicket;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class ReceptionTicketController extends Controller
{
    public function dashboard()
    {
        $approved = ReceptionTicket::with('project')
            ->where('status', 'approved')
            ->get(['travel_date', 'project_id']);

        $monthlyCounts = $approved
            ->groupBy(function ($ticket) {
                return Carbon::parse($ticket->travel_date)->format('Y-m');
            })
            ->map(fn($group) => $group->count())
            ->sortKeys();

        $projectCounts = $approved
            ->groupBy('project_id')
            ->map(function ($group) {
                $projectName = $group->first()->project?->name ?? 'N/A';
                return ['name' => $projectName, 'total' => $group->count()];
            })
            ->sortByDesc('total')
            ->values();

        $destinationCounts = $approved
            ->groupBy(function ($ticket) {
                return trim((string) $ticket->destination);
            })
            ->map(fn($group) => $group->count())
            ->filter(fn($count, $destination) => $destination !== '')
            ->sortDesc();

        $monthlyLabels = $monthlyCounts->keys()->values();
        $monthlyData = $monthlyCounts->values();

        $topProjects = $projectCounts->take(8)->values();
        $projectLabels = $topProjects->pluck('name');
        $projectData = $topProjects->pluck('total');

        return view('reception.dashboard', [
            'monthlyCounts' => $monthlyCounts,
            'projectCounts' => $projectCounts,
            'destinationCounts' => $destinationCounts,
            'monthlyLabels' => $monthlyLabels,
            'monthlyData' => $monthlyData,
            'projectLabels' => $projectLabels,
            'projectData' => $projectData,
        ]);
    }

    public function index(Request $request)
    {
        $query = $this->filteredApprovedQuery($request);

        $projects = Project::orderBy('name')->get();
        $tickets = $query->latest()->paginate(15)->withQueryString();

        return view('reception.tickets.index', compact('tickets', 'projects'));
    }

    public function show(ReceptionTicket $ticket)
    {
        if ($ticket->status !== 'approved') {
            abort(404);
        }

        $ticket->load(['user', 'project', 'pm', 'hod']);
        return view('reception.tickets.show', compact('ticket'));
    }

    public function export(Request $request)
    {
        $tickets = $this->filteredApprovedQuery($request)->get();

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="approved-tickets.csv"',
        ];

        $callback = function () use ($tickets) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, [
                'Requester',
                'Email',
                'Project',
                'Destination',
                'Travel Date',
                'Return Date',
                'Approved By (PM)',
                'Approved By (Director)',
            ]);

            foreach ($tickets as $ticket) {
                fputcsv($handle, [
                    $ticket->user?->name,
                    $ticket->user?->email,
                    $ticket->project?->name,
                    $ticket->destination,
                    $ticket->travel_date,
                    $ticket->return_date,
                    $ticket->pm?->name,
                    $ticket->hod?->name,
                ]);
            }

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function destination(string $destination)
    {
        $decoded = urldecode($destination);
        $tickets = ReceptionTicket::with(['user', 'project', 'pm', 'hod'])
            ->where('status', 'approved')
            ->where('destination', $decoded)
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('reception.destinations.show', [
            'destination' => $decoded,
            'tickets' => $tickets,
        ]);
    }

    private function filteredApprovedQuery(Request $request)
    {
        $query = ReceptionTicket::with(['user', 'project', 'pm', 'hod'])
            ->where('status', 'approved');

        if ($request->filled('project_id')) {
            $query->where('project_id', $request->project_id);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('travel_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('travel_date', '<=', $request->date_to);
        }

        if ($request->filled('destination')) {
            $query->where('destination', 'like', '%' . $request->destination . '%');
        }

        if ($request->filled('requester')) {
            $requester = $request->requester;
            $query->whereHas('user', function ($q) use ($requester) {
                $q->where('name', 'like', '%' . $requester . '%')
                    ->orWhere('email', 'like', '%' . $requester . '%');
            });
        }

        return $query;
    }
}
