<?php

namespace App\Http\Middleware;

use App\Models\Organization;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IdentifyTenant
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Get the organization slug from the first URL segment
        $slug = $request->segment(1);
        
        if (!$slug) {
            // Central domain routes (no slug)
            return $next($request);
        }

        // Find the organization
        $organization = Organization::where('slug', $slug)->first();

        if (!$organization) {
            abort(404, 'Organization not found');
        }

        // Set the current organization in the request and container
        $request->attributes->set('organization', $organization);
        app()->instance('current.organization', $organization);

        return $next($request);
    }
}
