<?php

namespace Mxavier\FilamentBillingKit\Services;

use Illuminate\Database\Eloquent\Model;
use Mxavier\FilamentBillingKit\Models\Plan;
use RuntimeException;

class EntitlementsManager
{
    protected ?Model $billable = null;

    public function forBillable(Model $billable): static
    {
        $instance = clone $this;
        $instance->billable = $billable;

        return $instance;
    }

    public function currentPlan(): ?Plan
    {
        $this->ensureBillable();

        if (method_exists($this->billable, 'currentPlan')) {
            return $this->billable->currentPlan();
        }

        $subscriptionName = config('filament-billing-kit.subscription_name', 'default');
        $subscription = $this->billable->subscription($subscriptionName);

        if (! $subscription || ! $subscription->valid()) {
            return null;
        }

        // Priorité : plan_id positionné lors de la création/mise à jour d'abonnement
        if ($subscription->plan_id) {
            return Plan::find($subscription->plan_id);
        }

        // Fallback : recherche par le price ID du premier item (driver Stripe/Cashier)
        $priceId = $subscription->items->first()?->stripe_price ?? null;

        return $priceId
            ? Plan::where('provider_price_id_monthly', $priceId)
                ->orWhere('provider_price_id_yearly', $priceId)
                ->first()
            : null;
    }

    public function hasFeature(string $key): bool
    {
        $plan = $this->currentPlan();

        if (! $plan) {
            return false;
        }

        $feature = $plan->getFeature($key);

        return $feature && $feature->isBoolean() && $feature->getValue() === true;
    }

    public function getFeatureLimit(string $key): ?int
    {
        $plan = $this->currentPlan();

        if (! $plan) {
            return null;
        }

        $feature = $plan->getFeature($key);

        return ($feature && $feature->isNumeric()) ? $feature->getValue() : null;
    }

    public function hasReachedLimit(string $key, int $currentUsage): bool
    {
        $limit = $this->getFeatureLimit($key);

        return $limit !== null && $currentUsage >= $limit;
    }

    public function getRemainingQuota(string $key, int $currentUsage): ?int
    {
        $limit = $this->getFeatureLimit($key);

        return $limit !== null ? max(0, $limit - $currentUsage) : null;
    }

    protected function ensureBillable(): void
    {
        if (! $this->billable) {
            throw new RuntimeException('No billable model set. Call forBillable() first.');
        }
    }
}
