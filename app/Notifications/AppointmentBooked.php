<?php

namespace App\Notifications;

use App\Models\Appointment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AppointmentBooked extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Appointment $appointment) {}

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
        // Future: add 'vonage' or 'twilio' for SMS
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'appointment_id'   => $this->appointment->id,
            'title'            => 'New Appointment Booked',
            'body'             => 'Appointment booked with Dr. ' . $this->appointment->doctor->name
                                . ' on ' . $this->appointment->appointment_date->format('d M Y')
                                . ' at ' . \Carbon\Carbon::parse($this->appointment->appointment_time)->format('h:i A') . '.',
            'action_url'       => route('appointments.show', $this->appointment->id),
            'icon'             => 'calendar-plus',
            'type'             => 'appointment_booked',
        ];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Appointment Confirmed — ' . tenant('clinic_name'))
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line('Your appointment has been booked successfully.')
            ->line('**Doctor:** Dr. ' . $this->appointment->doctor->name)
            ->line('**Date:** ' . $this->appointment->appointment_date->format('l, d F Y'))
            ->line('**Time:** ' . \Carbon\Carbon::parse($this->appointment->appointment_time)->format('h:i A'))
            ->line('**Visit Type:** ' . ucfirst(str_replace('_', ' ', $this->appointment->visit_type)))
            ->line('**Fee:** ৳' . number_format($this->appointment->fee, 2))
            ->action('View Appointment', route('appointments.show', $this->appointment->id))
            ->line('Thank you for choosing ' . tenant('clinic_name') . '.');
    }

    // Future SMS support
    // public function toVonage(object $notifiable): VonageMessage { ... }
}