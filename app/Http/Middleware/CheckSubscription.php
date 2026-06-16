<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckSubscription
{
    public function handle(Request $request, Closure $next): Response
    {
        $tenant = tenant();

        // No tenant context — skip (central app)
        if (!$tenant) {
            return $next($request);
        }

        // Subscription is active — allow through
        if ($tenant->isSubscriptionActive()) {
            return $next($request);
        }

        // Suspended by admin
        if (!$tenant->is_active) {
            return response()->view('tenant.subscription.suspended', [], 403);
        }

        // Subscription expired
        return response()->view('tenant.subscription.expired', compact('tenant'), 402);
    }
}