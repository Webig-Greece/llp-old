<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  $permission
     * @return mixed
     */
    public function handle(Request $request, Closure $next, $permission)
    {
        $user = Auth::user();

        if (!$user || !$user->roles->pluck('permissions')->flatten()->pluck('name')->contains($permission)) {
            // User does not have the required permission
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        return $next($request);
    }
}
