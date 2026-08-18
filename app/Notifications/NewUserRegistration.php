<?php

namespace App\Notifications;

use App\Models\UserRegistration;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class NewUserRegistration extends Notification
{
    use Queueable;

    public function __construct(
        public UserRegistration $registration
    ) {
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'registration_id' => $this->registration->id,
            'name' => $this->registration->name,
            'email' => $this->registration->email,
            'role' => $this->registration->role,
            'project_name' => $this->registration->project_name,
            'message' => 'New registration request from ' . $this->registration->name . ' (' . $this->registration->email . ') awaiting your approval.',
            'type' => 'warning',
        ];
    }
}
