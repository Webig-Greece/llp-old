<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\StripeService;

class PaymentController extends Controller
{
    protected $stripeService;

    public function __construct(StripeService $stripeService)
    {
        $this->stripeService = $stripeService;
    }

    public function createSubscription(Request $request)
    {
        $request->validate([
            'stripeToken' => 'required',
            'planId' => 'required'
        ]);

        // Retrieve the user
        $user = $request->user();

        // Create a subscription in Stripe
        $stripeData = $this->stripeService->createSubscription([
            'email' => $user->email,
            'name' => $user->name,
            'stripeToken' => $request->stripeToken
        ], $request->planId);

        // Store the StripeCustomer ID and Subscription ID in the User model
        $user->setStripeSubscription($stripeData['customerId'], $stripeData['subscriptionId']);

        return response()->json(['message' => 'Subscription successful']);
    }
}
