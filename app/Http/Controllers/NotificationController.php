<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * Mark a notification as read and redirect to its ticket.
     */
    public function read(string $id)
    {
        $notification = Auth::user()->notifications()->where('id', $id)->first();

        if ($notification) {
            $notification->markAsRead();
            $ticketId = $notification->data['ticket_id'] ?? null;
            if ($ticketId) {
                return redirect()->route('travel-requests.show', $ticketId);
            }
            $registrationId = $notification->data['registration_id'] ?? null;
            if ($registrationId) {
                return redirect()->route('user-registrations.index');
            }
        }

        return redirect()->route('dashboard');
    }

    /**
     * Mark all notifications as read for the current user.
     */
    public function markAllRead()
    {
        Auth::user()->unreadNotifications->markAsRead();
        return back();
    }
}
