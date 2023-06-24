<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use Illuminate\Http\Request;
use App\Mail\AppointmentReminder;
use Illuminate\Support\Facades\Mail;


class AppointmentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
        $this->middleware('permission:create-appointment', ['only' => ['store']]);
        $this->middleware('permission:update-appointment', ['only' => ['update']]);
        $this->middleware('permission:delete-appointment', ['only' => ['destroy']]);
    }

    public function index()
    {
        $appointments = Appointment::where('user_id', auth()->user()->id)->get();
        return response()->json($appointments);
    }

    public function store(Request $request)
    {
        $request->validate([
            'patient_id' => 'required|integer|exists:patients,id',
            'date' => 'required|date',
            'time' => 'required|date_format:H:i:s',
            'duration' => 'required|integer',
            'notes' => 'nullable|string'
        ]);

        $appointment = new Appointment([
            'user_id' => auth()->user()->id,
            'patient_id' => $request->patient_id,
            'date' => $request->date,
            'time' => $request->time,
            'duration' => $request->duration,
            'notes' => $request->notes
        ]);

        $appointment->save();

        // Send email notification
        Mail::to($appointment->patient->email)->send(new AppointmentReminder($appointment));

        return response()->json(['message' => 'Appointment created successfully', 'appointment' => $appointment], 201);
    }

    public function show($id)
    {
        $appointment = Appointment::find($id);

        if (!$appointment || $appointment->user_id != auth()->user()->id) {
            return response()->json(['message' => 'Appointment not found'], 404);
        }

        return response()->json($appointment);
    }

    public function update(Request $request, $id)
    {
        $appointment = Appointment::find($id);

        if (!$appointment || $appointment->user_id != auth()->user()->id) {
            return response()->json(['message' => 'Appointment not found'], 404);
        }

        $request->validate([
            'patient_id' => 'integer|exists:patients,id',
            'date' => 'date',
            'time' => 'date_format:H:i:s',
            'duration' => 'integer',
            'notes' => 'nullable|string'
        ]);

        $appointment->fill($request->all());
        $appointment->save();

        return response()->json(['message' => 'Appointment updated successfully', 'appointment' => $appointment]);
    }

    public function destroy($id)
    {
        $appointment = Appointment::find($id);

        if (!$appointment || $appointment->user_id != auth()->user()->id) {
            return response()->json(['message' => 'Appointment not found'], 404);
        }

        $appointment->delete();

        return response()->json(['message' => 'Appointment deleted successfully']);
    }
}
