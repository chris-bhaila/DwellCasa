<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureLocationSelected
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        if (auth()->user()->hasRole('super_admin') && !session('selected_location_id')) {
            // Allow the location switch request through, otherwise they can never set it
            if ($request->routeIs('admin.location.switch')) {
                return $next($request);
            }
            return redirect()->route('admin')->with('error', 'Please select a location to continue.');
        }

        return $next($request);
    }
}
