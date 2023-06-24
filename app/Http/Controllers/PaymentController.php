<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Stripe;
use Customer;
use Subscription;

class PaymentController extends Controller
{
    public function __construct()
    {
        Stripe::setApiKey(env('STRIPE_SECRET'));
    }

    public function createSubscription(Request $request)
    {
        $request->validate([
            'stripeToken' => 'required',
            'plan' => 'required'
        ]);

        // Create a new customer
        $customer = Customer::create([
            'source' => $request->stripeToken,
            'email' => $request->user()->email,
            'name' => $request->user()->name
        ]);

        // Create a new subscription
        $subscription = Subscription::create([
            'customer' => $customer->id,
            'items' => [['plan' => $request->plan]],
        ]);

        // Store subscription info in the database (to be implemented)
        // ...

        return response()->json(['message' => 'Subscription successful', 'subscription' => $subscription]);
    }
}
