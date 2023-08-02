<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;


class ProfessionalAccountService
{
    public function createSecondaryProfessionalAccount($data)
    {
        // Validate the input data
        $validatedData = Validator::make($data, [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'profession' => 'required|in:psychologist,counselor,coach,psychiatrist',
            'address' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'language' => 'nullable|string|max:10',
            // Add other necessary fields
        ])->validate();

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
            'account_type' => 'secondary',
            'first_name' => $validatedData['first_name'],
            'last_name' => $validatedData['last_name'],
            'email' => $validatedData['email'],
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
