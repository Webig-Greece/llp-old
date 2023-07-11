<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\SubscriptionPlan;

class AdminController extends Controller
{
    // Fetch dashboard data
    public function dashboard()
    {
        $users = User::count();
        $subscriptions = SubscriptionPlan::count();
        $revenue = SubscriptionPlan::sum('price');

        return response()->json([
            'users' => $users,
            'subscriptions' => $subscriptions,
            'revenue' => $revenue
        ]);
    }
}
