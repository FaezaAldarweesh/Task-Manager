<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\User;
use Illuminate\Http\Request;
use App\Services\ApiResponseService;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckPermission
{
    /**
     * Handle an incoming request.
     *
     * If the user is authenticated and has the required permission go
     * othorwize return unauthorized response
     * 
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle($request, Closure $next, $permission)
    {
        $user = User::find(Auth::id());

        if (Auth::check() && $user->hasPermission($permission)) {
            return $next($request);
        }
        return ApiResponseService::error(null, 'Unauthorized', 403);
    }
}
