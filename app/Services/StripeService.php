<?php

namespace App\Services;

use Stripe\Stripe;
use Stripe\Customer;
use Stripe\Subscription;

class StripeService
{
    public function __construct()
    {
        Stripe::setApiKey(env('STRIPE_SECRET'));
    }

    /**
     * Create a new subscription in Stripe.
     *
     * @param  array  $userData
     * @param  string  $planId
     * @return array
     */
    public function createSubscription($userData, $planId)
    {
        // Create a new customer in Stripe
        $customer = Customer::create([
            'email' => $userData['email'],
            'name' => $userData['name'],
            'source' => $userData['stripeToken']
        ]);

        // Create a new subscription in Stripe
        $subscription = Subscription::create([
            'customer' => $customer->id,
            'items' => [['plan' => $planId]],
        ]);

        return [
            'customerId' => $customer->id,
            'subscriptionId' => $subscription->id
        ];
    }
}
