<?php

namespace App\Services;

use App\Models\Company;

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
}
