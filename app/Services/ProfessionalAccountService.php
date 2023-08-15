<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\CreateSecondaryProfessionalAccountRequest;


class ProfessionalAccountService
{
    public function createSecondaryProfessionalAccount(CreateSecondaryProfessionalAccountRequest $request)
    {
        $validatedData = $request->validated();
        // Get the authenticated user
        $user = User::find(Auth::id());

        // Check if the user is eligible to create a secondary professional account
        if (!$user->canCreateAdditionalProfessionalAccount()) {
            throw new \Exception('User is not eligible to create a secondary professional account');
        }

        // Handle any additional charges
        // ...

        // Create the secondary professional account
        $secondaryAccount = new User([
            'account_type' => 'professional',
            'first_name' => $validatedData['first_name'],
            'last_name' => $validatedData['last_name'],
            'email' => $validatedData['email'],
            'password' => bcrypt($validatedData['password']),
            'profession' => $validatedData['profession'],
            'company_id' => $user->company_id,
            'branch_id' => $user->branch_id,
            'status' => 'active',
            'address' => $validatedData['address'] ?? null,
            'phone' => $validatedData['phone'] ?? null,
            'language' => $validatedData['language'] ?? null,
            // Add other necessary fields if required
        ]);


        // Associate the secondary account with the main account
        // ...

        // Save the secondary account
        $secondaryAccount->save();

        return $secondaryAccount;
    }
}
