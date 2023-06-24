<?php

namespace App\Mail;

use App\Models\Appointment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AppointmentReminder extends Mailable
{
    use Queueable, SerializesModels;

    protected $appointment;

    /**
     * Create a new message instance.
     *
     * @param Appointment $appointment
     */
    public function __construct(Appointment $appointment)
    {
        $this->appointment = $appointment;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Appointment Reminder')
            ->view('emails.appointment_reminder')
            ->with([
                'appointmentDate' => $this->appointment->scheduled_date,
                'patientName' => $this->appointment->patient->patient_name,
                'practitionerName' => $this->appointment->practitioner->name,
                'notes' => $this->appointment->notes
            ]);
    }
}
