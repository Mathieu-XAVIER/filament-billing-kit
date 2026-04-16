<?php

return [
    'plan' => [
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
    ],

    'subscription' => [
        'label' => 'Subscription',
        'plural_label' => 'Subscriptions',
        'statuses' => [
            'active' => 'Active',
            'trialing' => 'Trial',
            'past_due' => 'Past due',
            'canceled' => 'Canceled',
            'incomplete' => 'Incomplete',
            'incomplete_expired' => 'Expired',
            'paused' => 'Paused',
        ],
    ],

    'invoice' => [
        'label' => 'Invoice',
        'plural_label' => 'Invoices',
        'statuses' => [
            'paid' => 'Paid',
            'open' => 'Open',
            'draft' => 'Draft',
            'void' => 'Void',
            'uncollectible' => 'Uncollectible',
        ],
    ],

    'layout' => [
        'page_title' => 'Billing',
        'brand' => 'Billing',
        'nav' => [
            'aria_label' => 'Billing navigation',
            'plans' => 'Plans',
            'subscription' => 'My subscription',
            'invoices' => 'My invoices',
        ],
    ],

    'pages' => [
        'plans' => [
            'error_title' => 'An error occurred',
            'subscription_activated' => 'Subscription activated!',
            'subscription_activated_message' => 'Your subscription is now active. Welcome aboard!',
            'view_subscription' => 'View my subscription',
            'payment_canceled' => 'Payment canceled',
            'payment_canceled_message' => 'You canceled the payment process. No amount has been charged.',
            'back_to_plans' => 'Back to plans',
            'current_subscription_label' => 'Current subscription',
            'manage_billing' => 'Manage billing',
            'grace_period_ends' => 'Ends on :date',
            'title_change' => 'Change plan',
            'title_choose' => 'Choose your plan',
            'subtitle' => 'Start today, cancel anytime.',
            'monthly' => 'Monthly',
            'yearly' => 'Yearly',
            'empty' => 'No plans available at the moment.',
            'custom_quote' => 'Custom quote',
            'current_plan' => 'Current plan',
            'contact_us' => 'Contact us',
            'get_started' => 'Get started',
            'change_plan' => 'Change plan',
            'redirecting' => 'Redirecting…',
            'price_period_monthly' => 'mo',
            'price_period_yearly' => 'yr',
        ],

        'my_subscription' => [
            'title' => 'My Subscription',
            'error_title' => 'An error occurred',
            'grace_period_title' => 'Subscription on grace period',
            'grace_period_message' => 'Your subscription has been canceled and will end on <strong>:date</strong>. You can reactivate it before that date.',
            'no_subscription_title' => 'No active subscription',
            'no_subscription_message' => 'You have no active subscription.',
            'no_subscription_message_full' => 'You have no active subscription. Subscribe to a plan to access all features.',
            'no_subscription_cta' => 'Choose a plan →',
            'heading' => 'My current subscription',
            'plan_label' => 'Plan',
            'status_label' => 'Status',
            'trial_end' => 'Trial end',
            'scheduled_end' => 'Scheduled end',
            'renewal' => 'Renewal',
            'manage_billing' => 'Manage Billing',
            'no_active_subscription' => 'No active subscription.',
            'features_heading' => 'Included features',
        ],

        'my_invoices' => [
            'title' => 'My Invoices',
            'empty' => 'No invoices yet.',
            'columns' => [
                'number' => 'Invoice #',
                'amount' => 'Amount',
                'status' => 'Status',
                'date' => 'Date',
            ],
            'pdf' => 'PDF',
        ],
    ],

    'widgets' => [
        'active_subscriptions' => 'Active subscriptions',
        'ongoing_trials' => 'Ongoing trials',
        'failed_payments' => 'Failed payments',
    ],
];
