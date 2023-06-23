<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PatientRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'branch_id',
        'practitioner_id',
        'patient_name',
        'notes',
        'medical_history'
    ];

    /**
     * Get the branch that owns the patient record.
     */
    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    /**
     * Get the practitioner (user) that owns the patient record.
     */
    public function practitioner()
    {
        return $this->belongsTo(User::class, 'practitioner_id');
    }
}
