<?php

use Mxavier\FilamentBillingKit\Models\PlanFeature;

it('returns true for a boolean feature set to true', function () {
    $feature = new PlanFeature(['type' => 'boolean', 'value' => 'true']);

    expect($feature->getValue())->toBeTrue();
    expect($feature->isBoolean())->toBeTrue();
    expect($feature->isNumeric())->toBeFalse();
});

it('returns false for a boolean feature set to false', function () {
    $feature = new PlanFeature(['type' => 'boolean', 'value' => 'false']);

    expect($feature->getValue())->toBeFalse();
});

it('returns integer for a numeric feature', function () {
    $feature = new PlanFeature(['type' => 'numeric', 'value' => '50']);

    expect($feature->getValue())->toBe(50);
    expect($feature->isNumeric())->toBeTrue();
    expect($feature->isBoolean())->toBeFalse();
});

it('returns null getValue for unknown type', function () {
    $feature = new PlanFeature(['type' => 'unknown', 'value' => 'anything']);

    expect($feature->getValue())->toBeNull();
});
