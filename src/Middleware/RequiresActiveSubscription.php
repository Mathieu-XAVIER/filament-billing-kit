<?php

namespace Mxavier\FilamentBillingKit\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RequiresActiveSubscription
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! config('filament-billing-kit.require_subscription', false)) {
            return $next($request);
        }

        $billable = $this->resolveBillable($request);

        if (! $billable) {
            return $next($request);
        }

        $subscriptionName = config('filament-billing-kit.subscription_name', 'default');

        if (! $billable->subscribed($subscriptionName)) {
            $billingRoute = config('filament-billing-kit.billing_return_route');

            return redirect()->route($billingRoute);
        }

        return $next($request);
    }

    protected function resolveBillable(Request $request): mixed
    {
        $billableModel = config('filament-billing-kit.billable_model');

        if (config('filament-billing-kit.mode') === 'multi-tenant') {
            // In multi-tenant mode, resolve from the current tenant
            return filament()->getTenant();
        }

        return $request->user();
    }
}
