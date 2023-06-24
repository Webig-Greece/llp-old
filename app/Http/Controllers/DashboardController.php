<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function getUserDetails()
    {
        $user = Auth::user();
        $subscription = $user->subscription; // Assuming a relationship is set up

        return response()->json([
            'user' => $user,
            'subscription' => $subscription,
        ]);
    }
}
