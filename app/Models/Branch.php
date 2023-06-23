<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Branch extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'name',
        'address',
        'phone'
    ];

    /**
     * Get the company that owns the branch.
     */
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Get the users for the branch.
     */
    public function users()
    {
        return $this->hasMany(User::class);
    }

    /**
     * Get the patient records for the branch.
     */
    public function patientRecords()
    {
        return $this->hasMany(PatientRecord::class);
    }
}
