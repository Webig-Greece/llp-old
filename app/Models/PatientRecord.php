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
        'medical_history',
        'treatment_plan',
        'next_appointment'
    ];

    protected $encryptable = [
        'medical_history',
        'treatment_plan',
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

    // Encrypt Fields (Mutators)
    public function setMedicalHistoryAttribute($value)
    {
        $this->attributes['medical_history'] = encrypt($value);
    }

    public function setTreatmentPlanAttribute($value)
    {
        $this->attributes['treatment_plan'] = encrypt($value);
    }

    // Decrypt Fields (Accessors)
    public function getMedicalHistoryAttribute($value)
    {
        return decrypt($value);
    }

    public function getTreatmentPlanAttribute($value)
    {
        return decrypt($value);
    }
}
