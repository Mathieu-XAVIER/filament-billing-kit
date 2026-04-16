<?php

use Mxavier\FilamentBillingKit\Database\Factories\PlanFactory;
use Mxavier\FilamentBillingKit\Database\Factories\PlanFeatureFactory;
use Mxavier\FilamentBillingKit\Models\Plan;
use Mxavier\FilamentBillingKit\Models\PlanFeature;

uses(Mxavier\FilamentBillingKit\Tests\TestCase::class);

it('creates a plan with a boolean feature', function () {
    $plan = PlanFactory::new()->create();

    PlanFeatureFactory::new()->boolean(true)->create([
        'plan_id' => $plan->id,
        'key' => 'can_export',
    ]);

    $plan->refresh()->load('features');

    expect($plan->hasFeature('can_export'))->toBeFalse(); // hasFeature lives on billable
    expect($plan->getFeature('can_export'))->toBeInstanceOf(PlanFeature::class);
    expect($plan->getFeature('can_export')->getValue())->toBeTrue();
});

it('creates a plan with a numeric quota feature', function () {
    $plan = PlanFactory::new()->create();

    PlanFeatureFactory::new()->numeric(25)->create([
        'plan_id' => $plan->id,
        'key' => 'max_users',
    ]);

    $plan->refresh()->load('features');

    $feature = $plan->getFeature('max_users');

    expect($feature)->not->toBeNull();
    expect($feature->getValue())->toBe(25);
});

it('returns null for a non-existent feature key', function () {
    $plan = PlanFactory::new()->create();

    expect($plan->getFeature('non_existent'))->toBeNull();
});

it('active scope returns only active plans ordered by display_order', function () {
    PlanFactory::new()->inactive()->create(['display_order' => 1]);
    PlanFactory::new()->create(['display_order' => 2, 'is_active' => true]);
    PlanFactory::new()->create(['display_order' => 1, 'is_active' => true]);

    $plans = Plan::active()->get();

    expect($plans)->toHaveCount(2);
    expect($plans->first()->display_order)->toBe(1);
});
