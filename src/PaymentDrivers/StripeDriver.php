<?php

namespace Mxavier\FilamentBillingKit\PaymentDrivers;

use Illuminate\Database\Eloquent\Model;
use Mxavier\FilamentBillingKit\Contracts\PaymentDriverContract;
use Mxavier\FilamentBillingKit\Models\Plan;
use RuntimeException;

class StripeDriver implements PaymentDriverContract
{
    public function checkout(
        Model $billable,
        Plan $plan,
        string $billingPeriod,
        string $successUrl,
        string $cancelUrl
    ): string {
        $priceId = $billingPeriod === 'yearly'
            ? ($plan->provider_price_id_yearly ?? $plan->provider_price_id_monthly)
            : $plan->provider_price_id_monthly;

        if (! $priceId) {
            throw new RuntimeException('Ce plan n\'a pas de prix Stripe configuré pour cette période.');
        }

        $checkout = $billable
            ->newSubscription(config('filament-billing-kit.subscription_name', 'default'), $priceId)
            ->checkout([
                'success_url' => $successUrl,
                'cancel_url' => $cancelUrl,
            ]);

        return $checkout->url;
    }

    public function manageBilling(Model $billable, string $returnUrl): string
    {
        return $billable->billingPortalUrl($returnUrl);
    }

    public function hasYearlyPricing(Plan $plan): bool
    {
        return ! empty($plan->provider_price_id_yearly);
    }
}
