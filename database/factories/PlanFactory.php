<?php

namespace Mxavier\FilamentBillingKit\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Mxavier\FilamentBillingKit\Models\Plan;

class PlanFactory extends Factory
{
    protected $model = Plan::class;

    public function definition(): array
    {
        $name = $this->faker->words(2, true);

        return [
            'name' => ucwords($name),
            'slug' => Str::slug($name).'-'.Str::random(4),
            'description' => $this->faker->sentence(),
            'is_active' => true,
            'is_featured' => false,
            'is_custom_quote' => false,
            'sort_order' => $this->faker->numberBetween(0, 10),
            'marketing_badge' => null,
            'price_monthly' => $this->faker->randomElement([500, 1000, 2900, 4900, 9900]),
            'price_yearly' => null,
            'currency' => 'EUR',
            'trial_days' => null,
            'provider_price_id_monthly' => 'price_'.$this->faker->regexify('[A-Za-z0-9]{24}'),
            'provider_price_id_yearly' => null,
        ];
    }

    public function withYearlyPricing(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'price_yearly' => ($attributes['price_monthly'] ?? 2900) * 10,
                'provider_price_id_yearly' => 'price_'.$this->faker->regexify('[A-Za-z0-9]{24}'),
            ];
        });
    }

    public function inactive(): static
    {
        return $this->state(['is_active' => false]);
    }

    public function featured(): static
    {
        return $this->state(['is_featured' => true]);
    }

    public function withTrial(int $days = 14): static
    {
        return $this->state(['trial_days' => $days]);
    }

    public function customQuote(?string $contactUrl = null): static
    {
        return $this->state([
            'is_custom_quote' => true,
            'contact_url' => $contactUrl,
            'provider_price_id_monthly' => null,
            'provider_price_id_yearly' => null,
        ]);
    }
}
