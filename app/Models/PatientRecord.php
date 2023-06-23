<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PatientRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'practitioner_id',
        'patient_name',
        'notes',
        'appointment_date'
    ];

    /**
     * Get the practitioner who owns the patient record.
     */
    public function practitioner()
    {
        return $this->belongsTo(User::class, 'practitioner_id');
    }
}
