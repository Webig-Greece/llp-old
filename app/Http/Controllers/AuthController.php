<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Laravel\Sanctum\PersonalAccessToken;
use Illuminate\Support\Facades\Cookie;


class AuthController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'email' => 'required|string|email|unique:users',
            'password' => 'required|string|confirmed',
        ]);

        $user = new User([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password)
        ]);

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
                'message' => 'Unauthorized'
            ], 401);
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
        return response()->json($request->user());
    }

    public function upgrade(Request $request)
    {
        $user = User::find(Auth::id());

        // Validate the request data
        $request->validate([
            'subscription_plan' => 'required|string', // The name of the subscription plan the user is upgrading to
        ]);

        // Get the role associated with the subscription plan
        $role = Role::where('name', $request->subscription_plan)->first();

        if (!$role) {
            return response()->json([
                'message' => 'Invalid subscription plan'
            ], 400);
        }

        // If the user is currently a trial user, set subscribed_from_trial to true
        if ($user->hasRole('trial_user')) {
            $user->subscribed_from_trial = true;
        }

        // Update the user's role
        $user->roles()->sync($role);

        // Update the user's trial_ends_at field to null since they're no longer a trial user
        $user->trial_ends_at = null;

        $user->save();

        return response()->json([
            'message' => 'Successfully upgraded user!'
        ], 200);
    }
}
