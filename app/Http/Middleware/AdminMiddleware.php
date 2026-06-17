<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        // Check if user is authenticated and has admin email
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Please login to access admin panel.');
        }

        // You can change this condition based on your admin identification
        // For example, using a role column or specific email
        if (Auth::user()->email !== 'admin@jjhouse.com') {
            abort(403, 'Unauthorized access. Admin privileges required.');
        }

        return $next($request);
    }
}
