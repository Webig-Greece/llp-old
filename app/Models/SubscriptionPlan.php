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
}
