<?php

return [
    'label' => 'Plan',
    'plural_label' => 'Plans',
    'fields' => [
        'name' => 'Name',
        'slug' => 'Slug',
        'description' => 'Description',
        'is_active' => 'Active',
        'periodicity' => 'Billing period',
        'price' => 'Price (in cents)',
        'currency' => 'Currency',
        'trial_days' => 'Trial days',
        'provider_price_id' => 'Provider Price ID',
        'marketing_badge' => 'Badge',
        'display_order' => 'Display order',
    ],
    'periodicity' => [
        'monthly' => 'Monthly',
        'annual' => 'Annual',
    ],
];
