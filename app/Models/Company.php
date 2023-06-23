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
        'email'
    ];

    /**
     * Get the branches for the company.
     */
    public function branches()
    {
        return $this->hasMany(Branch::class);
    }
}
