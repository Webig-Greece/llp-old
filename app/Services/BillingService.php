<?php

namespace App\Services;

use App\Models\Company;
use App\Models\User;

class BillingService
{
    /**
     * Calculate the total subscription price for a company.
     *
     * @param  \App\Models\Company  $company
     * @return float
     */
    public function calculateCompanySubscriptionPrice(Company $company)
    {
        $basePrice = $company->subscriptionPlan->price;
        $pricePerUser = $company->subscriptionPlan->price_per_user;
        $numberOfUsers = $company->users->count();

        return $basePrice + ($pricePerUser * $numberOfUsers);
    }

    /**
     * Calculate the subscription price for an individual user.
     *
     * @param  \App\Models\User  $user
     * @return float
     */
    public function calculateUserSubscriptionPrice(User $user)
    {
        return $user->subscriptionPlan->price;
    }

    /**
     * Calculate the price with a discount applied.
     *
     * @param  float  $price
     * @param  float  $discountPercentage
     * @return float
     */
    public function applyDiscount($price, $discountPercentage)
    {
        return $price - ($price * ($discountPercentage / 100));
    }

    /**
     * Calculate the tax amount for the given price.
     *
     * @param  float  $price
     * @param  float  $taxRate
     * @return float
     */
    public function calculateTax($price, $taxRate)
    {
        return $price * ($taxRate / 100);
    }
}
