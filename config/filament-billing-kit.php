<?php

use Mxavier\FilamentBillingKit\PaymentDrivers\StripeDriver;

return [
    'mode' => env('BILLING_KIT_MODE', 'mono-tenant'),

    /*
    |--------------------------------------------------------------------------
    | Driver de paiement
    |--------------------------------------------------------------------------
    | Classe implémentant PaymentDriverContract. Par défaut : StripeDriver.
    | Pour utiliser un autre prestataire, créez votre propre driver et
    | référencez-le ici (FQCN) ou via BILLING_KIT_PAYMENT_DRIVER dans .env.
    |
    | Exemple : 'payment_driver' => App\Billing\PaddleDriver::class
    */
    'payment_driver' => env('BILLING_KIT_PAYMENT_DRIVER', StripeDriver::class),

    'billable_model' => env('BILLING_KIT_BILLABLE_MODEL', App\Models\User::class),
    'subscription_name' => env('BILLING_KIT_SUBSCRIPTION_NAME', 'default'),
    'enable_invoices' => env('BILLING_KIT_ENABLE_INVOICES', true),
    'enable_entitlements' => env('BILLING_KIT_ENABLE_ENTITLEMENTS', true),
    'enable_widgets' => env('BILLING_KIT_ENABLE_WIDGETS', true),
    'require_subscription' => env('BILLING_KIT_REQUIRE_SUBSCRIPTION', false),
    'billing_return_route' => env('BILLING_KIT_RETURN_ROUTE', 'filament.admin.pages.my-subscription'),
    'route_prefix' => env('BILLING_KIT_ROUTE_PREFIX', 'billing'),
    'route_middleware' => ['web', 'auth'],
    'contact_url' => env('BILLING_KIT_CONTACT_URL', null),
];
