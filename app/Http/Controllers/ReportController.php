<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function patientTreatmentSummary(Request $request)
    {
        $reportData = DB::table('patients')
            ->join('treatments', 'patients.id', '=', 'treatments.patient_id')
            ->join('appointments', 'patients.id', '=', 'appointments.patient_id')
            ->select('patients.name as patient_name', 'treatments.type as treatment_type', 'appointments.date as appointment_date')
            ->get();


        $report = [];
        foreach ($reportData as $row) {
            $report[$row->patient_name]['treatments'][] = [
                'type' => $row->treatment_type,
                'appointment_date' => $row->appointment_date,
            ];
        }

        return response()->json(['message' => 'Report generated successfully', 'data' => $report]);
    }

    public function appointmentStatistics(Request $request)
    {
        // Apply filters based on date range, patient, practitioner, and status
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $patientId = $request->input('patient_id');
        $practitionerId = $request->input('practitioner_id');
        $status = $request->input('status');

        $query = DB::table('appointments')
            ->select(DB::raw('status, COUNT(*) as count'))
            ->groupBy('status');

        if ($startDate) {
            $query->where('date', '>=', $startDate);
        }

        if ($endDate) {
            $query->where('date', '<=', $endDate);
        }

        if ($patientId) {
            $query->where('patient_id', $patientId);
        }

        if ($practitionerId) {
            $query->where('practitioner_id', $practitionerId);
        }

        if ($status) {
            $query->where('status', $status);
        }

        $statistics = $query->get();

        // Format the statistics as needed
        $report = [
            'total' => 0,
            'completed' => 0,
            'canceled' => 0,
            'upcoming' => 0,
        ];

        foreach ($statistics as $stat) {
            $report[$stat->status] = $stat->count;
            $report['total'] += $stat->count;
        }

        return response()->json($report);
    }

    public function financialReports(Request $request)
    {
        // Apply filters based on date range and subscription plan
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $subscriptionPlanId = $request->input('subscription_plan_id');

        $query = DB::table('subscriptions')
            ->join('subscription_plans', 'subscriptions.plan_id', '=', 'subscription_plans.id')
            ->select(DB::raw('SUM(subscription_plans.price) as total_revenue, COUNT(*) as total_subscriptions'))
            ->addSelect(DB::raw('SUM(subscriptions.additional_charges) as total_additional_charges'))
            ->addSelect(DB::raw('SUM(subscriptions.discounts) as total_discounts'));

        if ($startDate) {
            $query->where('subscriptions.start_date', '>=', $startDate);
        }

        if ($endDate) {
            $query->where('subscriptions.end_date', '<=', $endDate);
        }

        if ($subscriptionPlanId) {
            $query->where('subscription_plans.id', $subscriptionPlanId);
        }

        $report = $query->first();

        return response()->json($report);
    }

    public function professionalPerformanceAnalytics(Request $request)
    {
        // Define filters, such as date range, professional, etc.
        // ...

        // Query the database to gather the required data
        // ...

        // Process the data to calculate metrics like satisfaction scores, number of patients, etc.
        // ...

        // Return the report data as JSON
        // return response()->json($report);
    }

    public function usageStatistics(Request $request)
    {
        // Define filters, such as date range, user type, etc.
        // ...

        // Query the database to gather the required data, such as active users, feature usage, etc.
        // ...

        // Process the data to calculate the required statistics
        // ...

        // Return the report data as JSON
        // return response()->json($report);
    }
}
