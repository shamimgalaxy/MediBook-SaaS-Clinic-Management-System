<?php

namespace App\Notifications;

use App\Models\Appointment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AppointmentStatusChanged extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Appointment $appointment,
        public string $oldStatus
    ) {}

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toDatabase(object $notifiable): array
    {
        $status = $this->appointment->status;

        $titles = [
            'confirmed'   => 'Appointment Confirmed',
            'in_progress' => 'Your Visit Has Started',
            'completed'   => 'Appointment Completed',
            'cancelled'   => 'Appointment Cancelled',
        ];

        $icons = [
            'confirmed'   => 'circle-check',
            'in_progress' => 'stethoscope',
            'completed'   => 'clipboard-check',
            'cancelled'   => 'circle-x',
        ];

        return [
            'appointment_id' => $this->appointment->id,
            'title'          => $titles[$status] ?? 'Appointment Updated',
            'body'           => $this->buildBody(),
            'action_url'     => route('appointments.show', $this->appointment->id),
            'icon'           => $icons[$status] ?? 'calendar',
            'type'           => 'appointment_status_' . $status,
            'old_status'     => $this->oldStatus,
            'new_status'     => $status,
        ];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $status = $this->appointment->status;

        $subjects = [
            'confirmed'   => 'Appointment Confirmed',
            'in_progress' => 'Your Visit is Now in Progress',
            'completed'   => 'Appointment Completed — Thank You',
            'cancelled'   => 'Appointment Cancelled',
        ];

        $mail = (new MailMessage)
            ->subject(($subjects[$status] ?? 'Appointment Update') . ' — ' . tenant('clinic_name'))
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line($this->buildBody())
            ->line('**Doctor:** Dr. ' . $this->appointment->doctor->name)
            ->line('**Date:** ' . $this->appointment->appointment_date->format('l, d F Y'))
            ->line('**Time:** ' . \Carbon\Carbon::parse($this->appointment->appointment_time)->format('h:i A'));

        if ($status !== 'cancelled') {
            $mail->action('View Appointment', route('appointments.show', $this->appointment->id));
        }

        return $mail->line('— ' . tenant('clinic_name'));
    }

    private function buildBody(): string
    {
        $date   = $this->appointment->appointment_date->format('d M Y');
        $time   = \Carbon\Carbon::parse($this->appointment->appointment_time)->format('h:i A');
        $doctor = 'Dr. ' . $this->appointment->doctor->name;

        return match ($this->appointment->status) {
            'confirmed'   => "Your appointment with {$doctor} on {$date} at {$time} has been confirmed.",
            'in_progress' => "Your visit with {$doctor} has started. Please proceed to the consultation room.",
            'completed'   => "Your appointment with {$doctor} on {$date} has been completed. Thank you for visiting.",
            'cancelled'   => "Your appointment with {$doctor} on {$date} at {$time} has been cancelled.",
            default       => "Your appointment status has been updated to " . ucfirst(str_replace('_', ' ', $this->appointment->status)) . ".",
        };
    }
}