<?php

namespace App\Notifications;

use App\Models\TravelRequest;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TicketStatusUpdated extends Notification
{
    use Queueable;

    public function __construct(
        public TravelRequest $travelRequest,
        public string $message,
        public string $type = 'info'
    ) {
    }

    public function via(object $notifiable): array
    {
        $channels = ['database'];

        if ($notifiable instanceof User && $notifiable->hasRole('commercial-director')) {
            $channels[] = 'mail';
        }

        return $channels;
    }

    public function toMail(object $notifiable): MailMessage
    {
        $this->travelRequest->loadMissing(['user', 'project']);

        return (new MailMessage)
            ->subject('Travel Ticket: ' . $this->travelRequest->destination)
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line($this->message)
            ->line('Requester: ' . ($this->travelRequest->user?->name ?? 'N/A'))
            ->line('Project: ' . ($this->travelRequest->project?->name ?? 'N/A'))
            ->line('Route: ' . $this->travelRequest->origin . ' → ' . $this->travelRequest->destination)
            ->line('Travel date: ' . $this->travelRequest->travel_date)
            ->action('View ticket', route('travel-requests.show', $this->travelRequest))
            ->line('This message was sent by ' . config('app.name') . '.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'ticket_id' => $this->travelRequest->id,
            'destination' => $this->travelRequest->destination,
            'message' => $this->message,
            'type' => $this->type,
        ];
    }
}
