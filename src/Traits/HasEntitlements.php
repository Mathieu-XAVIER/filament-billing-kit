<?php

namespace Mxavier\FilamentBillingKit\Traits;

use Mxavier\FilamentBillingKit\Models\Plan;

trait HasEntitlements
{
    public function currentPlan(): ?Plan
    {
        $subscriptionName = config('filament-billing-kit.subscription_name', 'default');

        $subscription = $this->subscription($subscriptionName);

        if (! $subscription || ! $subscription->valid()) {
            return null;
        }

        // Priorité : plan_id positionné lors de la création/mise à jour d'abonnement
        if ($subscription->plan_id) {
            return Plan::find($subscription->plan_id);
        }

        // Fallback : recherche par le price ID du premier item (driver Stripe/Cashier)
        $priceId = $subscription->items->first()?->stripe_price ?? null;

        if (! $priceId) {
            return null;
        }

        return Plan::where('provider_price_id_monthly', $priceId)
            ->orWhere('provider_price_id_yearly', $priceId)
            ->first();
    }

    public function hasFeature(string $key): bool
    {
        $plan = $this->currentPlan();

        if (! $plan) {
            return false;
        }

        $feature = $plan->getFeature($key);

        if (! $feature || ! $feature->isBoolean()) {
            return false;
        }

        return $feature->getValue() === true;
    }

    public function getFeatureLimit(string $key): ?int
    {
        $plan = $this->currentPlan();

        if (! $plan) {
            return null;
        }

        $feature = $plan->getFeature($key);

        if (! $feature || ! $feature->isNumeric()) {
            return null;
        }

        return $feature->getValue();
    }

    public function hasReachedLimit(string $key, int $currentUsage): bool
    {
        $limit = $this->getFeatureLimit($key);

        if ($limit === null) {
            return false; // no limit = unlimited
        }

        return $currentUsage >= $limit;
    }

    public function getRemainingQuota(string $key, int $currentUsage): ?int
    {
        $limit = $this->getFeatureLimit($key);

        if ($limit === null) {
            return null; // unlimited
        }

        return max(0, $limit - $currentUsage);
    }
}
