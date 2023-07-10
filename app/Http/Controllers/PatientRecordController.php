<?php

namespace App\Http\Controllers;

use App\Models\PatientRecord;
use Illuminate\Http\Request;

class PatientRecordController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the patient records for the authenticated user.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return PatientRecord::where('user_id', auth()->id())->with('branch')->paginate(10);
    }

    /**
     * Store a newly created patient record in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'branch_id' => 'required|integer|exists:branches,id',
            'patient_name' => 'required|string|max:255',
            'notes' => 'nullable|string',
            'medical_history' => 'nullable|string',
            'treatment_plan' => 'nullable|string',
            'next_appointment' => 'nullable|date'
        ]);

        $patientRecord = new PatientRecord($request->all());
        $patientRecord->user_id = auth()->user()->id;
        $patientRecord->save();

        return response()->json(['message' => 'Patient record created successfully', 'data' => $patientRecord], 201);
    }

    /**
     * Display the specified patient record.
     *
     * @param  \App\Models\PatientRecord  $patientRecord
     * @return \Illuminate\Http\Response
     */
    public function show(PatientRecord $patientRecord)
    {
        $this->authorize('view', $patientRecord);
        // Check if the authenticated user's ID matches the user_id of the patient record
        if (auth()->user()->id !== $patientRecord->user_id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        return $patientRecord;
    }

    /**
     * Update the specified patient record in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\PatientRecord  $patientRecord
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, PatientRecord $patientRecord)
    {
        $request->validate([
            'branch_id' => 'sometimes|required|integer|exists:branches,id',
            'patient_name' => 'sometimes|required|string|max:255',
            'notes' => 'nullable|string',
            'medical_history' => 'nullable|string',
            'treatment_plan' => 'nullable|string',
            'next_appointment' => 'nullable|date'
        ]);

        $this->authorize('update', $patientRecord);
        // Check if the authenticated user's ID matches the user_id of the patient record
        if (auth()->user()->id !== $patientRecord->user_id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $patientRecord->update($request->all());
        return response()->json($patientRecord, 200);
    }

    /**
     * Remove the specified patient record from storage.
     *
     * @param  \App\Models\PatientRecord  $patientRecord
     * @return \Illuminate\Http\Response
     */
    public function destroy(PatientRecord $patientRecord)
    {
        $this->authorize('delete', $patientRecord);
        // Check if the authenticated user's ID matches the user_id of the patient record
        if (auth()->user()->id !== $patientRecord->user_id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $patientRecord->delete();
        return response()->json(null, 204);
    }
}
