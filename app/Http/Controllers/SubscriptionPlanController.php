<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SubscriptionPlan;

class SubscriptionPlanController extends Controller
{
    /**
     * Display a listing of the subscription plans.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $plans = SubscriptionPlan::all();

        return response()->json($plans);
    }

    /**
     * Store a newly created subscription plan in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $plan = SubscriptionPlan::create($request->all());

        return response()->json($plan, 201);
    }

    /**
     * Display the specified subscription plan.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $plan = SubscriptionPlan::findOrFail($id);

        return response()->json($plan);
    }

    /**
     * Update the specified subscription plan in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $plan = SubscriptionPlan::findOrFail($id);
        $plan->update($request->all());

        return response()->json($plan, 200);
    }

    /**
     * Remove the specified subscription plan from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        SubscriptionPlan::destroy($id);

        return response()->json(null, 204);
    }
}
