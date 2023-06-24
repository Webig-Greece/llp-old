<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\SubscriptionPlan;
use App\Models\User;
use Illuminate\Http\Request;

class UserSubscriptionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function subscribe(Request $request)
    {
        $request->validate([
            'subscription_plan_id' => 'required|integer|exists:subscription_plans,id',
            'user_id' => 'required|integer|exists:users,id'
        ]);

        $subscriptionPlan = SubscriptionPlan::find($request->subscription_plan_id);
        $user = User::find($request->user_id);

        // Check if the user is part of a company
        if ($user->company) {
            $price = $this->calculateCompanySubscriptionPrice($user->company);
        } else {
            $price = $subscriptionPlan->price;
        }

        // Handle the payment process (to be implemented)
        // ...

        // Create a subscription record for the user (to be implemented)
        // ...

        return response()->json(['message' => 'Subscription successful', 'price' => $price]);
    }

    public function renew(Request $request)
    {
        // Validate the request data
        // Handle the payment process for renewal
        // Update the subscription record
    }

    public function cancel(Request $request)
    {
        // Validate the request data
        // Cancel the user's subscription
    }

    /**
     * Calculate the total subscription price for a company.
     *
     * @param  \App\Models\Company  $company
     * @return float
     */
    private function calculateCompanySubscriptionPrice(Company $company)
    {
        $basePrice = $company->subscriptionPlan->price;
        $pricePerUser = $company->subscriptionPlan->price_per_user;
        $numberOfUsers = $company->users->count();

        return $basePrice + ($pricePerUser * $numberOfUsers);
    }
}
