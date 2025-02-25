<?php

namespace App\Notifications;

use App\Models\Application;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ApplicationCreatedNotification extends Notification
{
    public function __construct(public Application $application) {}

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('New application received')
            ->line('Name: ' . $this->application->name)
            ->line('Email: ' . $this->application->email)
            ->line('Phone: ' . $this->application->phone)
            ->line($this->application->body);
    }

    public function toArray($notifiable): array
    {
        return [];
    }
}
