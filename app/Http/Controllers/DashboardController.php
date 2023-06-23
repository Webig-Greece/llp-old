<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function getUserDetails()
    {
        $user = Auth::user();

        // Here you can retrieve additional information such as subscription plan, billing information, etc.
        // For example: $subscription = $user->subscription;

        return response()->json([
            'user' => $user,
            // 'subscription' => $subscription,
        ]);
    }
}
