<div>
    <div class="fbk-section">
        <h2 class="fbk-section-heading">{{ trans('filament-billing-kit::front/my_invoices.title') }}</h2>

        @if($invoices->isEmpty())
            <div style="display:flex;flex-direction:column;align-items:center;justify-content:center;padding:3rem 0;color:#9ca3af;">
                <svg xmlns="http://www.w3.org/2000/svg" style="height:2.5rem;width:2.5rem;margin-bottom:0.75rem;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                          d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                <p style="font-size:0.875rem;">{{ trans('filament-billing-kit::front/my_invoices.empty') }}</p>
            </div>
        @else
            <div class="overflow-hidden">
                <div class="fbk-invoice-header">
                    <p class="fbk-invoice-header__cell">{{ trans('filament-billing-kit::front/my_invoices.columns.number') }}</p>
                    <p class="fbk-invoice-header__cell">{{ trans('filament-billing-kit::front/my_invoices.columns.amount') }}</p>
                    <p class="fbk-invoice-header__cell">{{ trans('filament-billing-kit::front/my_invoices.columns.status') }}</p>
                    <p class="fbk-invoice-header__cell">{{ trans('filament-billing-kit::front/my_invoices.columns.date') }}</p>
                </div>

                @foreach($invoices as $invoice)
                    @php
                        $statusLabel = match($invoice->status) {
                            'paid'  => trans('filament-billing-kit::front/my_invoices.statuses.paid'),
                            'open'  => trans('filament-billing-kit::front/my_invoices.statuses.open'),
                            default => $invoice->status,
                        };
                        $statusClass = match($invoice->status) {
                            'paid'  => 'fbk-invoice-badge--paid',
                            'open'  => 'fbk-invoice-badge--open',
                            default => 'fbk-invoice-badge--default',
                        };
                    @endphp
                    <div class="fbk-invoice-row">
                        <p class="fbk-invoice-number">
                            {{ $invoice->invoice_number ?? '—' }}
                        </p>
                        <p class="fbk-invoice-amount">
                            {{ number_format($invoice->amount_paid / 100, 2) }} {{ strtoupper($invoice->currency) }}
                        </p>
                        <span class="fbk-invoice-badge {{ $statusClass }}">
                            {{ $statusLabel }}
                        </span>
                        <div class="fbk-invoice-meta">
                            <p class="fbk-invoice-date">
                                {{ $invoice->created_at->format('d/m/Y') }}
                            </p>
                            @if($invoice->invoice_pdf)
                                <a
                                    href="{{ $invoice->invoice_pdf }}"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    class="fbk-invoice-pdf-btn"
                                >
                                    <svg style="width:0.875rem;height:0.875rem;" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                        <path d="M10.75 2.75a.75.75 0 00-1.5 0v8.614L6.295 8.235a.75.75 0 10-1.09 1.03l4.25 4.5a.75.75 0 001.09 0l4.25-4.5a.75.75 0 00-1.09-1.03l-2.955 3.129V2.75z"/>
                                        <path d="M3.5 12.75a.75.75 0 00-1.5 0v2.5A2.75 2.75 0 004.75 18h10.5A2.75 2.75 0 0018 15.25v-2.5a.75.75 0 00-1.5 0v2.5c0 .69-.56 1.25-1.25 1.25H4.75c-.69 0-1.25-.56-1.25-1.25v-2.5z"/>
                                    </svg>
                                    {{ trans('filament-billing-kit::front/my_invoices.pdf') }}
                                </a>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>

            @if($invoices->hasPages())
                <div style="margin-top:1rem;">
                    {{ $invoices->links() }}
                </div>
            @endif
        @endif
    </div>
</div>
