<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\TravelRequest;

class TicketStatusUpdated extends Notification
{
    use Queueable;

    public $travelRequest;
    public $message;
    public $type;

    /**
     * Create a new notification instance.
     */
    public function __construct(TravelRequest $travelRequest, string $message, string $type = 'info')
    {
        $this->travelRequest = $travelRequest;
        $this->message = $message;
        $this->type = $type;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database']; // Only saving to the database for the bell icon
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'ticket_id' => $this->travelRequest->id,
            'destination' => $this->travelRequest->destination,
            'message' => $this->message,
            'type' => $this->type, // e.g., 'success', 'warning', 'error', 'info'
        ];
    }
}
