<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Treatment;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class TreatmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Auth::user();
        // $user = User::find(Auth::id());
        $treatments = Treatment::where('user_id', $user->id)->get();
        return response()->json(['data' => $treatments]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $user = Auth::user();
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

        $treatmentData = $request->all();
        $treatmentData['user_id'] = $user->id;
        $treatment = Treatment::create($treatmentData);

        return response()->json(['message' => 'Treatment plan created successfully', 'data' => $treatment]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $user = Auth::user();
        $treatment = Treatment::where('user_id', $user->id)->findOrFail($id);
        return response()->json(['data' => $treatment]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $user = Auth::user();
        $treatment = Treatment::where('user_id', $user->id)->findOrFail($id);
        $treatment->update($request->all());
        return response()->json(['message' => 'Treatment plan updated successfully', 'data' => $treatment]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $user = Auth::user();
        $treatment = Treatment::where('user_id', $user->id)->findOrFail($id);
        $treatment->delete();
        return response()->json(['message' => 'Treatment plan deleted successfully']);
    }
}
