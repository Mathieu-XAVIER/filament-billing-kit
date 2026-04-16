<?php

namespace Mxavier\FilamentBillingKit\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Mxavier\FilamentBillingKit\Models\Plan;
use Mxavier\FilamentBillingKit\Models\PlanFeature;

class PlanFeatureFactory extends Factory
{
    protected $model = PlanFeature::class;

    public function definition(): array
    {
        $type = $this->faker->randomElement(['boolean', 'numeric']);

        return [
            'plan_id' => Plan::factory(),
            'key' => $this->faker->unique()->slug(2, '_'),
            'type' => $type,
            'value' => $type === 'boolean' ? 'true' : (string) $this->faker->numberBetween(1, 100),
            'label' => $this->faker->words(3, true),
        ];
    }

    public function boolean(bool $value = true): static
    {
        return $this->state([
            'type' => 'boolean',
            'value' => $value ? 'true' : 'false',
        ]);
    }

    public function numeric(int $value = 50): static
    {
        return $this->state([
            'type' => 'numeric',
            'value' => (string) $value,
        ]);
    }
}
