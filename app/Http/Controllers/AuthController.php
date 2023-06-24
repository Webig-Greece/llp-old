<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Mail\WelcomeEmail;
use Illuminate\Support\Facades\Mail;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'email' => 'required|string|email|unique:users',
            'password' => 'required|string|confirmed',
            'role' => 'required|string|in:admin,psychologist,counselor,coach'
        ]);

        $user = new User([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password)
        ]);

        $user->save();

        // Assign selected role to new user
        $role = Role::where('name', $request->role)->first();
        $user->roles()->attach($role);

        // Send welcome email
        Mail::to($user->email)->send(new WelcomeEmail($user->name));

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

        $user = $request->user();
        $token = $user->createToken('Personal Access Token')->accessToken;

        // Return additional user information
        return response()->json([
            'user' => $user,
            'token' => $token
        ]);
    }

    public function logout(Request $request)
    {
        // Revoke the user's token
        $request->user()->token()->revoke();

        return response()->json([
            'message' => 'Successfully logged out'
        ]);
    }

    public function userProfile(Request $request)
    {
        // Return the user's profile
        return response()->json($request->user());
    }
}
