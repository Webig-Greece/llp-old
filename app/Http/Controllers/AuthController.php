<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use App\Models\SubscriptionPlan;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Mail\PasswordResetMail;
use Laravel\Sanctum\PersonalAccessToken;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Services\ProfessionalAccountService;
use App\Http\Requests\CreateSecondaryProfessionalAccountRequest;
use SoapClient;

use Illuminate\Support\Facades\Log;



class AuthController extends Controller
{

    protected $professionalAccountService;

    public function __construct(ProfessionalAccountService $professionalAccountService)
    {
        $this->professionalAccountService = $professionalAccountService;
    }

    public function register(Request $request)
    {
        $request->validate([
            'firstName' => 'required|string',
            'lastName' => 'required|string',
            'email' => 'required|string|email|unique:users',
            'password' => 'required|string|confirmed|min:8',
            'vatNumber' => 'required|string|min:9|alpha_num',
            'acceptTerms' => 'required|accepted',
            'profession' => 'required|in:psychologist,counselor,coach,psychiatrist',
            'roleIdentity' => 'required|in:freelancer,company',
            'language' => 'required|string|min:2',
            'company_name' => 'nullable|string'
        ]);

        // Extract the first two characters of the VAT number as the country code
        $countryCode = substr($request->input('vatNumber'), 0, 2);
        $vatNumber = substr($request->input('vatNumber'), 2);

        // Verify the VAT number
        $vatResult = $this->verifyVATNumber($countryCode, $vatNumber);

        if (!$vatResult || !$vatResult->valid) {
            return response()->json(['message' => 'Invalid VAT number'], 400);
        }

        // Use the company name from the VIES API if available, otherwise use the provided name
        $companyName = $vatResult->name ?? ($request->input('company_name') ?? ($request->input('firstName') . ' ' . $request->input('lastName')));

        // Create the new Company
        $company = new Company([
            'name' => $companyName, // Store the company name if available
            'vat_number' => $request->input('vatNumber'),
            'address' => $vatResult->address ?? null, // Store the address if available
            'vat_verification_date' => $vatResult->requestDate, // Store the request date
        ]);
        $company->save();

        $user = new User([
            'first_name' => $request->input('firstName'),
            'last_name' => $request->input('lastName'),
            'email' => $request->input('email'),
            'password' => bcrypt($request->input('password')),
            'vat_number' => $request->input('vatNumber'),
            'profession' => $request->input('profession'),
            'company_id' => $company->id, // Set the company_id to the ID of the newly created company
        ]);

        // Sets is_freelancer flag accordingly
        $user->is_freelancer = ($request->roleIdentity == "freelancer");
        // Sets Language preference to user account from registration
        $user->language = $request->language;

        // Set the trial_ends_at field to 10 days from now
        $user->trial_ends_at = now()->addDays(10);
        $user->save();

        // Assign default role to new user
        $role = Role::where('name', 'trial_user')->first();
        $user->roles()->attach($role);

        // Send welcome email with verification link
        $user->sendEmailVerificationNotification();

        return response()->json([
            'message' => 'Successfully created user!'
        ], 201);
    }


    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string'
        ]);

        if (!Auth::attempt($request->only(['email', 'password']))) {
            return response()->json([
                'message' => 'Wrong username or password'
            ], 401);
        }

        // Check if the user is soft deleted
        $user = Auth::user();
        if ($user->deleted_at !== null) {
            return response()->json([
                'message' => 'This account has been deleted.'
            ], 403);
        }

        // $user = $request->user();
        $user = $request->user()->load('roles'); // Eager load the role
        $tokenResult = $user->createToken('Personal Access Token');
        $token = $tokenResult->plainTextToken;

        // Return additional user information
        return response()->json([
            'user' => $user,
            'token' => $token
        ]);
    }

    public function logout(Request $request)
    {
        // Get the token ID from the Authorization header
        $tokenId = $request->bearerToken();

        // Revoke the token
        if ($tokenId) {
            $token = PersonalAccessToken::findToken($tokenId);
            if ($token) {
                $token->delete();
                // Clear the token cookie
                Cookie::queue(Cookie::forget('laravel_session'));

                // Clear the XSRF-TOKEN cookie
                Cookie::queue(Cookie::forget('XSRF-TOKEN'));
            }
        }

        return response()->json([
            'message' => 'Successfully logged out'
        ]);
    }

    public function userProfile(Request $request)
    {
        // Return the user's profile
        return response()->json(['user' => $request->user()]);
    }

    public function changeSubscription(Request $request)
    {
        $user = User::find(Auth::id());

        // Validate the request data
        $request->validate([
            'subscription_plan' => 'required|string', // The name of the subscription plan the user is changing to
            'additional_secretaries' => 'nullable|integer|min:0', // The number of additional secretary accounts the user wants to add
        ]);

        // Fetch the subscription plan the user wants to change to
        $newSubscriptionPlan = SubscriptionPlan::where('name', $request->subscription_plan)->first();

        if (!$newSubscriptionPlan) {
            return response()->json([
                'message' => 'Invalid subscription plan'
            ], 400);
        }

        // If the user is currently a trial user
        if ($user->hasRole('trial_user')) {
            $user->subscribed_from_trial = true;
            $user->trial_ends_at = null; // End the trial
        }

        // Update the user's subscription plan
        $user->subscription_plan_id = $newSubscriptionPlan->id;

        // Get the subscription role based on the new subscription plan
        $subscriptionRoleName = $newSubscriptionPlan->getSubscriptionRoleForPlan();
        $subscriptionRole = Role::where('name', $subscriptionRoleName)->first();

        if (!$subscriptionRole) {
            return response()->json([
                'message' => 'Role not found'
            ], 400);
        }

        // Detach all subscription roles from the user (to ensure they only have one subscription role)
        $allSubscriptionRoles = Role::whereIn('name', ['trial_user', 'basicPlanRole', 'premiumPlanRole'])->get();
        $user->roles()->detach($allSubscriptionRoles);

        // Attach the new subscription role to the user
        $user->roles()->attach($subscriptionRole);

        $user->save();

        return response()->json([
            'message' => 'Successfully changed subscription!'
        ], 200);
    }


    public function updateProfile(Request $request)
    {
        $user = User::find(Auth::id());

        // Validate the request data
        $request->validate([
            'first_name' => 'string|max:255',
            'last_name' => 'string|max:255',
            'email' => 'string|email|max:255|unique:users,email,' . $user->id,
            'password' => 'string|min:8|confirmed',
            'default_template' => 'required|in:BIRP,DAP',
        ]);

        // Update the user's profile
        $user->first_name = $request->first_name ?? $user->first_name;
        $user->last_name = $request->last_name ?? $user->last_name;
        $user->email = $request->email ?? $user->email;
        $user->default_template  = $request->default_template ?? $user->default_template;

        if ($request->password) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        return response()->json(['message' => 'Profile updated successfully']);
    }


    public function deleteAccount(Request $request)
    {
        $user = User::find(Auth::id());

        // Soft delete the user
        $user->delete();

        return response()->json(['message' => 'Account deleted successfully']);
    }

    public function restoreUser(Request $request, $deletedUserId)
    {
        $currentUser = User::find(Auth::id());

        // Only allow admins to restore users
        if (!$currentUser->hasRole('admin')) {
            return response()->json([
                'message' => 'Unauthorized'
            ], 403);
        }

        // Find the user including soft deleted users
        $deletedUser = User::withTrashed()->where('id', $deletedUserId)->first();

        if (!$deletedUser) {
            return response()->json([
                'message' => 'User not found'
            ], 404);
        }

        // Restore the user
        $deletedUser->restore();

        return response()->json([
            'message' => 'User restored successfully'
        ]);
    }

    public function requestPasswordReset(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);

        $user = User::where('email', $request->email)->first();

        // Generate a token for the password reset
        $token = Str::random(60);

        // Store the token in the password_reset_tokens table
        DB::table('password_reset_tokens')->insert([
            'email' => $user->email,
            'token' => $token,
            'created_at' => Carbon::now(),
        ]);

        // Send the password reset email
        Mail::to($user->email)->send(new PasswordResetMail($token, $user));

        return response()->json([
            'message' => 'Password reset email sent.'
        ]);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|confirmed|min:6',
        ]);

        // Check if the token is valid
        $tokenData = DB::table('password_reset_tokens')
            ->where('token', $request->token)
            ->where('email', $request->email)
            ->first();

        if (!$tokenData) {
            return response()->json([
                'message' => 'Invalid token.'
            ], 400);
        }

        // Check if the token has expired
        if (Carbon::parse($tokenData->created_at)->addMinutes(60)->isPast()) {
            return response()->json([
                'message' => 'Token has expired.'
            ], 400);
        }

        // Update the user's password
        $user = User::where('email', $request->email)->first();
        $user->password = Hash::make($request->password);
        $user->save();

        // Delete the token
        DB::table('password_reset_tokens')->where('email', $user->email)->delete();

        return response()->json([
            'message' => 'Password reset successful.'
        ]);
    }

    public function addSecretary(Request $request, $id)
    {
        $request->validate([
            'firstName' => 'required|string',
            'lastName' => 'required|string',
            'email' => 'required|string|email|unique:users',
            'password' => 'required|string|confirmed|min:8',
        ]);

        // Only allow professionals to add secretaries
        $user = User::findOrFail($id);

        if (!$user->isProfessional()) {
            return response()->json([
                'message' => 'Unauthorized'
            ], 403);
        }

        // Check if the professional already has a secretary
        $secretaryCount = User::where('company_id', $user->company_id)->where('profession', 'secretary')->count();
        if ($secretaryCount >= 1) {
            return response()->json([
                'message' => 'You have reached your maximum number of secretary accounts.'
            ], 400);
        }

        $secretary = new User([
            'first_name' => $request->input('firstName'),
            'last_name' => $request->input('lastName'),
            'email' => $request->input('email'),
            'password' => bcrypt($request->input('password')),
            'account_type' => 'secretary',
            'company_id' => $user->company_id, // Set the company_id to the ID of the professional's company
        ]);

        $secretary->save();

        // Assign secretary role to new user
        $role = Role::where('name', 'secretary')->first();
        $secretary->roles()->attach($role);

        return response()->json([
            'message' => 'Successfully added secretary!'
        ], 201);
    }

    public function createSecondaryProfessionalAccount(CreateSecondaryProfessionalAccountRequest $request)
    {
        try {
            $secondaryAccount = $this->professionalAccountService->createSecondaryProfessionalAccount($request);
            return response()->json(['message' => 'Secondary professional account created successfully', 'data' => $secondaryAccount], 201);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }

    public function createSoapClient()
    {
        return new TestableSoapClient("https://ec.europa.eu/taxation_customs/vies/services/checkVatService.wsdl");
    }

    public function verifyVATNumber(string $countryCode, string $vatNumber): object
    {
        try {
            $client = $this->createSoapClient();
            $result = $client->checkVat(['countryCode' => $countryCode, 'vatNumber' => $vatNumber]);
            // Log::info('SOAP Client Result:', ['result' => $result]);

            if ($result === null) {
                throw new \Exception('SOAP Client returned null.');
            }

            return $result;
        } catch (\Exception $e) {
            // Log::error('SOAP Client Error:', ['error' => $e->getMessage()]);
            return (object) ['valid' => false];
        }
    }
}

// Overwite __doRequest to make it work
class TestableSoapClient extends \SoapClient
{
    public function __doRequest($request, $location, $action, $version, $one_way = 0)
    {
        // Log::info('SOAP Request:', ['request' => $request]);

        $response = parent::__doRequest($request, $location, $action, $version, $one_way);

        // Log::info('SOAP Response:', ['response' => $response]);

        return $response;
    }
}
