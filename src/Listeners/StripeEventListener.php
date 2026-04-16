<?php

namespace Mxavier\FilamentBillingKit\Listeners;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Laravel\Cashier\Events\InvoicePaymentFailed;
use Laravel\Cashier\Events\InvoicePaymentSucceeded;
use Laravel\Cashier\Events\SubscriptionCreated;
use Laravel\Cashier\Events\SubscriptionDeleted;
use Laravel\Cashier\Events\SubscriptionUpdated;
use Mxavier\FilamentBillingKit\Models\Plan;

class StripeEventListener
{
    public function handleSubscriptionCreated(SubscriptionCreated $event): void
    {
        $subscription = $event->subscription;

        if (! $subscription) {
            return;
        }

        $priceId = $this->extractPriceIdFromSubscription($event->payload['data']['object'] ?? []);

        if ($priceId) {
            $plan = Plan::where('provider_price_id_monthly', $priceId)
                ->orWhere('provider_price_id_yearly', $priceId)
                ->first();

            if ($plan) {
                DB::table('subscriptions')
                    ->where('stripe_id', $subscription->stripe_id)
                    ->update(['plan_id' => $plan->id]);
            }
        }
    }

    public function handleSubscriptionUpdated(SubscriptionUpdated $event): void
    {
        $subscription = $event->subscription;

        if (! $subscription) {
            return;
        }

        $newPriceId = $this->extractPriceIdFromSubscription($event->payload['data']['object'] ?? []);

        if (! $newPriceId) {
            return;
        }

        $newPlan = Plan::where('provider_price_id_monthly', $newPriceId)
            ->orWhere('provider_price_id_yearly', $newPriceId)
            ->first();

        if (! $newPlan) {
            return;
        }

        $oldPlanId = $subscription->plan_id;

        if ($oldPlanId !== $newPlan->id) {
            DB::table('subscription_plan_changes')->insert([
                'subscription_id' => $subscription->stripe_id,
                'from_plan_id' => $oldPlanId,
                'to_plan_id' => $newPlan->id,
                'changed_at' => now(),
                'source' => 'stripe_webhook',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::table('subscriptions')
                ->where('stripe_id', $subscription->stripe_id)
                ->update(['plan_id' => $newPlan->id]);
        }
    }

    public function handleSubscriptionDeleted(SubscriptionDeleted $event): void
    {
        Log::info('[filament-billing-kit] Subscription deleted', [
            'stripe_id' => $event->payload['data']['object']['id'] ?? null,
        ]);
    }

    public function handleInvoicePaymentSucceeded(InvoicePaymentSucceeded $event): void
    {
        $this->upsertInvoiceFromPayload($event->payload['data']['object'] ?? [], 'paid');
    }

    public function handleInvoicePaymentFailed(InvoicePaymentFailed $event): void
    {
        $this->upsertInvoiceFromPayload($event->payload['data']['object'] ?? [], 'open');
    }

    protected function upsertInvoiceFromPayload(array $invoice, ?string $forcedStatus = null): void
    {
        if (empty($invoice['id'])) {
            return;
        }

        $customerId = $invoice['customer'] ?? null;

        if (! $customerId) {
            return;
        }

        $billableModel = config('filament-billing-kit.billable_model', \App\Models\User::class);

        $billable = $billableModel::where('stripe_id', $customerId)->first();

        if (! $billable) {
            return;
        }

        DB::table('billing_invoices')->upsert(
            [
                'provider_invoice_id' => $invoice['id'],
                'billable_type' => get_class($billable),
                'billable_id' => $billable->getKey(),
                'provider_subscription_id' => $invoice['subscription'] ?? null,
                'amount_due' => $invoice['amount_due'] ?? 0,
                'amount_paid' => $invoice['amount_paid'] ?? 0,
                'currency' => strtoupper($invoice['currency'] ?? 'USD'),
                'status' => $forcedStatus ?? $invoice['status'] ?? 'open',
                'invoice_pdf' => $invoice['invoice_pdf'] ?? null,
                'invoice_number' => $invoice['number'] ?? null,
                'billing_period_start' => isset($invoice['period_start'])
                    ? date('Y-m-d H:i:s', $invoice['period_start'])
                    : null,
                'billing_period_end' => isset($invoice['period_end'])
                    ? date('Y-m-d H:i:s', $invoice['period_end'])
                    : null,
                'paid_at' => ($forcedStatus === 'paid' || $invoice['status'] === 'paid')
                    ? now()
                    : null,
                'created_at' => isset($invoice['created'])
                    ? date('Y-m-d H:i:s', $invoice['created'])
                    : now(),
                'updated_at' => now(),
            ],
            ['provider_invoice_id'],
            [
                'amount_due',
                'amount_paid',
                'status',
                'invoice_pdf',
                'invoice_number',
                'paid_at',
                'updated_at',
            ]
        );
    }

    protected function extractPriceIdFromSubscription(array $object): ?string
    {
        return $object['items']['data'][0]['price']['id']
            ?? $object['plan']['id']
            ?? null;
    }
}
