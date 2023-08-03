<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class CreateSecondaryProfessionalAccountRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Get the authenticated user
        $user = User::find(Auth::id());

        // Check if the user can create an additional professional account
        return $user->canCreateAdditionalProfessionalAccount();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'profession' => 'required|in:psychologist,counselor,coach,psychiatrist',
            'address' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'language' => 'nullable|string|max:10',
        ];
    }
}
