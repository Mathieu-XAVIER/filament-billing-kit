<?php

namespace Mxavier\FilamentBillingKit\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Plan extends Model
{
    use HasFactory;

    protected $table = 'plans';

    protected $fillable = [
        'name',
        'slug',
        'description',
        'is_active',
        'is_featured',
        'sort_order',
        'marketing_badge',
        'price_monthly',
        'price_yearly',
        'currency',
        'trial_days',
        'provider_price_id_monthly',
        'provider_price_id_yearly',
        'is_custom_quote',
        'contact_url',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
        'is_custom_quote' => 'boolean',
        'sort_order' => 'integer',
        'price_monthly' => 'integer',
        'price_yearly' => 'integer',
        'trial_days' => 'integer',
    ];

    public function features(): HasMany
    {
        return $this->hasMany(PlanFeature::class);
    }

    public function getFeature(string $key): ?PlanFeature
    {
        return $this->features->firstWhere('key', $key);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true)->orderBy('sort_order');
    }

    protected static function newFactory(): \Mxavier\FilamentBillingKit\Database\Factories\PlanFactory
    {
        return \Mxavier\FilamentBillingKit\Database\Factories\PlanFactory::new();
    }
}
