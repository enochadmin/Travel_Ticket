<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ReceptionBookingController extends Controller
{
    public function create(Request $request)
    {
        $ticketId = $request->query('ticket_id');
        // Attempt to load ticket if provided
        $ticket = null;
        if ($ticketId) {
            $ticket = \App\Models\TravelRequest::with(['user', 'project'])->find($ticketId);
        }

        return view('reception.bookings.create', compact('ticket'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'ticket_id' => 'required|integer|exists:travel_requests,id',
            'carrier' => 'nullable|string|max:255',
            'pnr' => 'nullable|string|max:255',
            'booking_reference' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        // Lightweight: do not persist booking model (not present). Instead flash success and redirect.
        $ticket = \App\Models\TravelRequest::find($data['ticket_id']);

        return redirect()->route('reception.tickets.show', $ticket)
            ->with('success', 'Booking flow completed (placeholder) for ticket #' . $ticket->id);
    }
}
