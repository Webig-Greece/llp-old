<?php

namespace App\Policies;

use App\Models\PatientRecord;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class PatientRecordPolicy
{

    public function view(User $user, PatientRecord $patientRecord)
    {
        return $user->id === $patientRecord->practitioner_id;
    }

    public function update(User $user, PatientRecord $patientRecord)
    {
        return $user->id === $patientRecord->practitioner_id;
    }

    public function delete(User $user, PatientRecord $patientRecord)
    {
        return $user->id === $patientRecord->practitioner_id;
    }
}
