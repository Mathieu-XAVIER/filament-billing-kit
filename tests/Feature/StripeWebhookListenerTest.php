<?php

use Illuminate\Support\Facades\DB;
use Laravel\Cashier\Events\InvoicePaymentSucceeded;
use Laravel\Cashier\Events\SubscriptionCreated;
use Mxavier\FilamentBillingKit\Database\Factories\PlanFactory;
use Mxavier\FilamentBillingKit\Listeners\StripeEventListener;

uses(Mxavier\FilamentBillingKit\Tests\TestCase::class);

it('resolves plan_id on subscription when SubscriptionCreated fires', function () {
    $plan = PlanFactory::new()->create(['provider_price_id_monthly' => 'price_test_123']);

    // Insert a fake Cashier subscription row
    DB::table('subscriptions')->insert([
        'user_id' => 1,
        'name' => 'default',
        'stripe_id' => 'sub_abc',
        'stripe_status' => 'active',
        'stripe_price' => 'price_test_123',
        'quantity' => 1,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $payload = [
        'data' => [
            'object' => [
                'id' => 'sub_abc',
                'items' => [
                    'data' => [
                        ['price' => ['id' => 'price_test_123']],
                    ],
                ],
            ],
        ],
    ];

    $subscription = Laravel\Cashier\Subscription::where('stripe_id', 'sub_abc')->first();

    $listener = new StripeEventListener;
    $listener->handleSubscriptionCreated(new SubscriptionCreated($subscription, $payload));

    expect(
        DB::table('subscriptions')->where('stripe_id', 'sub_abc')->value('plan_id')
    )->toBe($plan->id);
});

it('caches invoice data on InvoicePaymentSucceeded', function () {
    // Create a user with a stripe_id
    DB::table('users')->insert([
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => 'hashed',
        'stripe_id' => 'cus_abc',
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $payload = [
        'data' => [
            'object' => [
                'id' => 'in_test_001',
                'customer' => 'cus_abc',
                'subscription' => 'sub_abc',
                'amount_due' => 2900,
                'amount_paid' => 2900,
                'currency' => 'usd',
                'status' => 'paid',
                'invoice_pdf' => 'https://stripe.com/invoice.pdf',
                'number' => 'INV-0001',
                'period_start' => now()->startOfMonth()->timestamp,
                'period_end' => now()->endOfMonth()->timestamp,
                'created' => now()->timestamp,
            ],
        ],
    ];

    $listener = new StripeEventListener;
    $listener->handleInvoicePaymentSucceeded(new InvoicePaymentSucceeded(null, $payload));

    $this->assertDatabaseHas('billing_invoices', [
        'provider_invoice_id' => 'in_test_001',
        'status' => 'paid',
        'amount_paid' => 2900,
        'invoice_number' => 'INV-0001',
    ]);
});
