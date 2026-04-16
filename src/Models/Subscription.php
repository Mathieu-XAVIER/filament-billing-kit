<?php

namespace Mxavier\FilamentBillingKit\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Laravel\Cashier\Subscription as CashierSubscription;

class Subscription extends CashierSubscription
{
    public function plan(): BelongsTo
    {
        return $this->belongsTo(Plan::class);
    }

    public function planChanges(): HasMany
    {
        return $this->hasMany(SubscriptionPlanChange::class, 'subscription_id', 'stripe_id');
    }
}
