<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubscriptionPlan extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'price',
        'duration_in_days'
    ];

    /**
     * Get the companies associated with the subscription plan.
     */
    public function companies()
    {
        return $this->hasMany(Company::class);
    }

    public function allowsAdditionalProfessionalAccounts()
    {
        return $this->allows_additional_professional_accounts;
    }

    public function getSubscriptionRoleForPlan()
    {
        // Define a mapping of subscription plan names to roles
        $planToRoleMap = [
            'basic' => 'basicPlanRole',
            'premium' => 'premiumPlanRole',
            'trial' => 'trialUserRole',
            // ... add other plans and their corresponding roles as needed
        ];

        // Return the role associated with the current subscription plan's name
        return $planToRoleMap[$this->name] ?? null;
    }
}
