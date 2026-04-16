<?php

use Illuminate\Support\Facades\Route;
use Mxavier\FilamentBillingKit\Livewire\MyInvoicesPage;
use Mxavier\FilamentBillingKit\Livewire\MySubscriptionPage;
use Mxavier\FilamentBillingKit\Livewire\SubscriptionPage;

Route::middleware(config('filament-billing-kit.route_middleware', ['web', 'auth']))
    ->prefix(config('filament-billing-kit.route_prefix', 'billing'))
    ->name('billing.')
    ->group(function () {
        Route::get('/', SubscriptionPage::class)->name('index');
        Route::get('/subscription', MySubscriptionPage::class)->name('subscription');
        Route::get('/invoices', MyInvoicesPage::class)->name('invoices');
    });
