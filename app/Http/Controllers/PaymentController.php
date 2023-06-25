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
        try {
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
        } catch (\Exception $e) {
            // Handle the exception (e.g., log the error, return an error response)
            return response()->json(['message' => 'Error creating subscription', 'error' => $e->getMessage()], 500);
        }
    }

    public function handleWebhook(Request $request)
    {
        $payload = $request->all();

        // Handle the event
        switch ($payload['type']) {
            case 'invoice.paid':
                // Handle successful invoice payment
                break;
            case 'invoice.payment_failed':
                // Handle failed invoice payment
                break;
            case 'customer.subscription.created':
                // Handle subscription create
                break;
            case 'customer.subscription.deleted':
                // Handle subscription cancellation
                break;
                // ... handle other event types
            default:
                // Unexpected event type
                return response()->json(['message' => 'Unexpected event type'], 400);
        }

        return response()->json(['message' => 'Webhook received']);
    }

    public function updateSubscription(Request $request)
    {
        $request->validate([
            'planId' => 'required'
        ]);

        $user = $request->user();

        // Call the StripeService to update the subscription
        $this->stripeService->updateSubscription($user->stripe_subscription_id, $request->planId);

        return response()->json(['message' => 'Subscription updated successfully']);
    }

    public function cancelSubscription(Request $request)
    {
        $user = $request->user();

        // Call the StripeService to cancel the subscription
        $this->stripeService->cancelSubscription($user->stripe_subscription_id);

        return response()->json(['message' => 'Subscription canceled successfully']);
    }
}
