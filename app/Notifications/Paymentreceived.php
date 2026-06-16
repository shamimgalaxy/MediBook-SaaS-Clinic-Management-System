<?php

namespace App\Notifications;

use App\Models\Appointment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PaymentReceived extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Appointment $appointment) {}

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'appointment_id' => $this->appointment->id,
            'title'          => 'Payment Received',
            'body'           => 'Payment of ৳' . number_format($this->appointment->fee, 2)
                              . ' received via ' . ucfirst($this->appointment->payment_method)
                              . ' for your appointment on ' . $this->appointment->appointment_date->format('d M Y') . '.',
            'action_url'     => route('appointments.show', $this->appointment->id),
            'icon'           => 'credit-card',
            'type'           => 'payment_received',
        ];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Payment Receipt — ' . tenant('clinic_name'))
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line('We have received your payment. Here are the details:')
            ->line('**Amount:** ৳' . number_format($this->appointment->fee, 2))
            ->line('**Method:** ' . ucfirst($this->appointment->payment_method))
            ->line('**Doctor:** Dr. ' . $this->appointment->doctor->name)
            ->line('**Date:** ' . $this->appointment->appointment_date->format('l, d F Y'))
            ->action('View Appointment', route('appointments.show', $this->appointment->id))
            ->line('Thank you for choosing ' . tenant('clinic_name') . '.');
    }
}