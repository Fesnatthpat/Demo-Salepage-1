<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

use Illuminate\Support\Facades\Auth;

class CheckUserProfileCompletion
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            $user = Auth::user();
            if (is_null($user->date_of_birth) || is_null($user->gender) || is_null($user->age)) {
                if (!$request->routeIs('profile.completion') && !$request->routeIs('profile.store')) {
                    return redirect()->route('profile.completion');
                }
            }
        }

        return $next($request);
    }
}
