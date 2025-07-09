<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckWelcomeSession
{
    public function handle(Request $request, Closure $next)
    {
        // Skip check for welcome page and set-session route
        if ($request->is('/') || $request->is('set-session')) {
            return $next($request);
        }

        // Check if user has visited welcome page in this session
        if (!session('visited_welcome')) {
            return redirect('/');
        }

        return $next($request);
    }
}
