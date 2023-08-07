<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Notifications\CustomVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'password',
        'vat_number',
        'accept_terms',
        'account_type',
        'profession',
        'default_template',
        'company_id',
        'branch_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    /**
     * Get the company that the user belongs to.
     */
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Get the branch that the user belongs to.
     */
    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    /**
     * Get the patient records created by the user (practitioner).
     */
    public function patientRecords()
    {
        return $this->hasMany(PatientRecord::class, 'practitioner_id');
    }

    /**
     * The roles that belong to the user.
     */
    public function roles()
    {
        return $this->belongsToMany(Role::class, 'role_user');
    }

    public function hasRole($roleName)
    {
        return $this->roles()->where('name', $roleName)->first() != null;
    }


    public function subscriptionPlan()
    {
        return $this->belongsTo(SubscriptionPlan::class);
    }


    /**
     * Check if the user has an active subscription.
     *
     * @return bool
     */
    public function hasActiveSubscription()
    {
        // Check if the user has a Stripe Subscription ID and the trial period has not expired
        return $this->stripe_subscription_id !== null || $this->trial_ends_at > now();
    }

    public function sendEmailVerificationNotification()
    {
        $this->notify(new CustomVerifyEmail);
    }

    /**
     * Set the Stripe Customer ID and Subscription ID for the user.
     *
     * @param  string  $customerId
     * @param  string  $subscriptionId
     * @return void
     */
    public function setStripeSubscription($customerId, $subscriptionId)
    {
        $this->stripe_customer_id = $customerId;
        $this->stripe_subscription_id = $subscriptionId;
        $this->save();
    }


    public function isProfessional()
    {
        return in_array($this->profession, ['psychologist', 'counselor', 'coach', 'psychiatrist']);
    }

    public function canCreateBranch()
    {
        // Check if the user has the 'create-branch' permission
        return $this->hasPermission('create-branch');
    }

    public function hasPermission($permissionName)
    {
        // Check if the user has the specified permission
        return $this->roles->pluck('permissions')->flatten()->pluck('name')->contains($permissionName);
    }

    public function canCreateAdditionalProfessionalAccount()
    {
        // Check the user's account type and other relevant conditions
        // Example: Check if the user is a professional and has a specific subscription plan
        return $this->account_type == 'main' && $this->subscriptionPlanAllowsAdditionalAccounts();
    }

    public function subscriptionPlanAllowsAdditionalAccounts()
    {
        return $this->subscriptionPlan->allowsAdditionalProfessionalAccounts();
    }
}
