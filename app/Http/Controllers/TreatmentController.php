<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Treatment;

class TreatmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'template_type' => 'required|in:BIRP,DAP',
            'behavior_or_data' => 'required',
            'intervention_or_assessment' => 'required',
            'response' => 'nullable',
            'plan' => 'required',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date',
        ]);

        $treatment = Treatment::create($request->all());

        return response()->json(['message' => 'Treatment plan created successfully', 'data' => $treatment]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
