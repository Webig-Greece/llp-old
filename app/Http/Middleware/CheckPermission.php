<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class CheckPermission
{
    public function handle($request, Closure $next, ...$permissions)
    {
        $user = Auth::user();

        if (!$user || !$this->hasAnyPermission($user, $permissions)) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        return $next($request);
    }

    private function hasAnyPermission($user, $permissions)
    {
        foreach ($permissions as $permission) {
            if ($user->roles->pluck('permissions')->flatten()->pluck('name')->contains($permission)) {
                return true;
            }
        }
        return false;
    }
}
