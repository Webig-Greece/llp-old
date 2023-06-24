<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckSubscription
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();

        // Check if the user is an admin, in trial period or has an active subscription
        if ($user->hasRole('admin') || $user->trial_ends_at > now() || $user->hasActiveSubscription()) {
            return $next($request);
        }

        return response()->json(['error' => 'Subscription required'], 403);
    }
}
