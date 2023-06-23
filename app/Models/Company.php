<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'vat_number',
        'address',
        'phone',
        'email',
        'billing_address',
        'subscription_plan_id',
        'subscription_expiry'
    ];

    /**
     * Get the subscription plan associated with the company.
     */
    public function subscriptionPlan()
    {
        return $this->belongsTo(SubscriptionPlan::class);
    }

    /**
     * Get the branches for the company.
     */
    public function branches()
    {
        return $this->hasMany(Branch::class);
    }

    /**
     * Get the users for the company.
     */
    public function users()
    {
        return $this->hasMany(User::class);
    }
}
