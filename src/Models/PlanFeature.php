<?php

namespace Mxavier\FilamentBillingKit\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PlanFeature extends Model
{
    use HasFactory;

    protected $table = 'plan_features';

    protected $fillable = [
        'plan_id',
        'key',
        'type',
        'value',
        'label',
    ];

    public function plan(): BelongsTo
    {
        return $this->belongsTo(Plan::class);
    }

    public function isBoolean(): bool
    {
        return $this->type === 'boolean';
    }

    public function isNumeric(): bool
    {
        return $this->type === 'numeric';
    }

    public function getValue(): bool|int|null
    {
        if ($this->isBoolean()) {
            return filter_var($this->value, FILTER_VALIDATE_BOOLEAN);
        }

        if ($this->isNumeric()) {
            return (int) $this->value;
        }

        return null;
    }

    protected static function newFactory(): \Mxavier\FilamentBillingKit\Database\Factories\PlanFeatureFactory
    {
        return \Mxavier\FilamentBillingKit\Database\Factories\PlanFeatureFactory::new();
    }
}
