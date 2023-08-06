<?php

namespace App\Services;

use App\Models\Company;
use App\Models\User;

class BillingService
{
    const SECRETARY_ACCOUNT_CHARGE = 3; // €3 for secretary account
    const EXTRA_PROFESSIONAL_ACCOUNT_CHARGE = 5; // €5 for extra professional account

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

        $totalUsers = $company->users->count();
        $totalSecretaryAccounts = User::where('role', 'secretary')->where('company_id', $user->company_id)->count();
        $totalExtraProfessionalAccounts = User::where('role', 'professional')->where('company_id', $user->company_id)->count();

        $totalPrice = $basePrice + ($totalUsers * $pricePerUser);
        $totalPrice += ($totalSecretaryAccounts * self::SECRETARY_ACCOUNT_CHARGE);
        $totalPrice += ($totalExtraProfessionalAccounts * self::EXTRA_PROFESSIONAL_ACCOUNT_CHARGE);

        return $totalPrice;
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
