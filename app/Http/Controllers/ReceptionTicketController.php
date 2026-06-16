<?php

namespace App\Http\Controllers;

use App\Models\ReceptionTicket;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReceptionTicketController extends Controller
{
    public function dashboard()
    {
        $approvedTotal = ReceptionTicket::where('status', 'approved')->count();
        $archivedTotal = ReceptionTicket::whereNotNull('archived_at')->count();
        $pendingProcessing = max($approvedTotal - $archivedTotal, 0);

        $recentPending = ReceptionTicket::with(['user', 'project'])
            ->where('status', 'approved')
            ->whereNull('archived_at')
            ->orderBy('travel_date')
            ->take(6)
            ->get();

        return view('reception.dashboard', [
            'approvedTotal' => $approvedTotal,
            'archivedTotal' => $archivedTotal,
            'pendingProcessing' => $pendingProcessing,
            'recentPending' => $recentPending,
        ]);
    }

    public function index(Request $request)
    {
        $query = $this->filteredApprovedQuery($request);

        $projects = Project::orderBy('name')->get();
        $tickets = $query->latest()->paginate(15)->withQueryString();

        return view('reception.tickets.index', compact('tickets', 'projects'));
    }

    public function archived(Request $request)
    {
        $query = $this->filteredArchivedQuery($request);

        $projects = Project::orderBy('name')->get();
        $tickets = $query->latest('archived_at')->paginate(15)->withQueryString();

        return view('reception.tickets.archived', compact('tickets', 'projects'));
    }

    public function process(Request $request)
    {
        $validated = $request->validate([
            'ticket_ids' => 'required|array',
            'ticket_ids.*' => 'integer|exists:travel_requests,id',
        ]);

        $count = ReceptionTicket::where('status', 'approved')
            ->whereNull('archived_at')
            ->whereIn('id', $validated['ticket_ids'])
            ->update([
                'archived_at' => now(),
                'archived_by' => Auth::id(),
            ]);

        return redirect()->route('reception.tickets.index')
            ->with('success', $count . ' ticket(s) processed and archived.');
    }

    public function processAndBook(Request $request)
    {
        $validated = $request->validate([
            'ticket_ids' => 'required|array',
            'ticket_ids.*' => 'integer|exists:travel_requests,id',
        ]);

        $count = ReceptionTicket::where('status', 'approved')
            ->whereNull('archived_at')
            ->whereIn('id', $validated['ticket_ids'])
            ->update([
                'archived_at' => now(),
                'archived_by' => Auth::id(),
            ]);

        $firstTicketId = $validated['ticket_ids'][0] ?? null;

        return redirect()->route('reception.bookings.create', ['ticket_id' => $firstTicketId])
            ->with('success', $count . ' ticket(s) processed and archived. Ready for booking.');
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
            ->where('status', 'approved')
            ->whereNull('archived_at');

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

    private function filteredArchivedQuery(Request $request)
    {
        $query = ReceptionTicket::with(['user', 'project', 'pm', 'hod', 'archivedBy'])
            ->whereNotNull('archived_at');

        if ($request->filled('project_id')) {
            $query->where('project_id', $request->project_id);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('archived_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('archived_at', '<=', $request->date_to);
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
