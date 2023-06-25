<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Appointment;
use Illuminate\Support\Facades\Mail;
use App\Mail\AppointmentReminder;

class SendAppointmentReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'send:appointment-reminders';


    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send appointment reminders to patients';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Get all appointments that are happening in the next 24 hours
        $appointments = Appointment::whereBetween('date', [now(), now()->addDay()])->get();

        // Loop through each appointment and send a reminder
        foreach ($appointments as $appointment) {
            // Send email (assuming the patient's email is stored in the appointment)
            Mail::to($appointment->patient_email)->send(new AppointmentReminder($appointment));

            // Optionally, send an SMS (you can use a service like Twilio for this)
        }

        $this->info('Appointment reminders sent successfully');
    }
}
