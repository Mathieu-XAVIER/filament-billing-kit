<?php

namespace Mxavier\FilamentBillingKit\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SubscriptionPlanChange extends Model
{
    protected $table = 'subscription_plan_changes';

    protected $fillable = [
        'subscription_id',
        'from_plan_id',
        'to_plan_id',
        'changed_at',
        'source',
    ];

    protected $casts = [
        'changed_at' => 'datetime',
    ];

    public function fromPlan(): BelongsTo
    {
        return $this->belongsTo(Plan::class, 'from_plan_id');
    }

    public function toPlan(): BelongsTo
    {
        return $this->belongsTo(Plan::class, 'to_plan_id');
    }
}
