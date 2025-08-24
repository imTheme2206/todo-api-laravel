<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ForceJsonResponse
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        // Force all API requests to expect JSON
        $request->headers->set('Accept', 'application/json');

        $response = $next($request);

        // Ensure JSON content-type on responses where applicable
        if (method_exists($response, 'header')) {
            $response->header('Content-Type', 'application/json');
        }

        return $response;
    }
}
