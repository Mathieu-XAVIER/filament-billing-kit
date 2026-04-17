<?php

namespace Mxavier\FilamentBillingKit\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class BillingInvoice extends Model
{
    protected $table = 'billing_invoices';

    protected $fillable = [
        'provider_invoice_id',
        'billable_type',
        'billable_id',
        'provider_subscription_id',
        'amount_due',
        'amount_paid',
        'currency',
        'status',
        'invoice_pdf',
        'invoice_number',
        'billing_period_start',
        'billing_period_end',
        'paid_at',
    ];

    protected $casts = [
        'amount_due' => 'integer',
        'amount_paid' => 'integer',
        'billing_period_start' => 'datetime',
        'billing_period_end' => 'datetime',
        'paid_at' => 'datetime',
    ];

    public function billable(): MorphTo
    {
        return $this->morphTo();
    }

    public function getDisplayNumberAttribute(): string
    {
        return $this->invoice_number ?? $this->provider_invoice_id;
    }

    public function getFormattedAmountAttribute(): string
    {
        return number_format($this->amount_paid / 100, 2).' '.$this->currency;
    }

    public function isPaid(): bool
    {
        return $this->status === 'paid';
    }
}
