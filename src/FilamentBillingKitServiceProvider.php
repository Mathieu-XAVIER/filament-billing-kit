<?php

namespace Mxavier\FilamentBillingKit;

use Filament\Support\Assets\Css;
use Filament\Support\Facades\FilamentAsset;
use Illuminate\Support\Facades\Event;
use Livewire\Livewire;
use Mxavier\FilamentBillingKit\Contracts\PaymentDriverContract;
use Mxavier\FilamentBillingKit\Livewire\MyInvoicesPage;
use Mxavier\FilamentBillingKit\Livewire\MySubscriptionPage;
use Mxavier\FilamentBillingKit\Livewire\SubscriptionPage;
use Mxavier\FilamentBillingKit\PaymentDrivers\StripeDriver;
use Mxavier\FilamentBillingKit\Services\EntitlementsManager;
use Spatie\LaravelPackageTools\Commands\InstallCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class FilamentBillingKitServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('filament-billing-kit')
            ->hasConfigFile('filament-billing-kit')
            ->hasMigrations([
                '0001_create_plans_table',
                '0002_create_plan_features_table',
                '0003_create_subscriptions_table',
                '0004_create_subscription_plan_changes_table',
                '0005_create_invoices_cache_table',
            ])
            ->hasTranslations()
            ->hasViews('filament-billing-kit')
            ->hasRoute('web')
            ->runsMigrations()
            ->hasInstallCommand(function (InstallCommand $command) {
                $command
                    ->publishConfigFile()
                    ->publishMigrations()
                    ->askToRunMigrations()
                    ->endWith(function (InstallCommand $command) {
                        $command->info('✅ filament-billing-kit installed!');
                        $command->info('');
                        $command->info('Next steps:');
                        $command->info('1. Add HasEntitlements to your billable model.');
                        $command->info('2. Add FilamentBillingKitPlugin::make() to your PanelProvider.');
                        $command->info('3. Set BILLING_KIT_PAYMENT_DRIVER in .env (default: stripe).');
                        $command->info('   For Stripe: set STRIPE_WEBHOOK_SECRET in .env.');
                        $command->info('4. Optional — publish assets: php artisan vendor:publish --tag=filament-billing-kit-assets');
                    });
            });
    }

    public function packageBooted(): void
    {
        $this->app->singleton(EntitlementsManager::class);

        // Bind the payment driver
        $this->app->singleton(
            PaymentDriverContract::class,
            config('filament-billing-kit.payment_driver', StripeDriver::class)
        );

        Livewire::component('filament-billing-kit-subscription-page', SubscriptionPage::class);
        Livewire::component('filament-billing-kit-my-subscription-page', MySubscriptionPage::class);
        Livewire::component('filament-billing-kit-my-invoices-page', MyInvoicesPage::class);

        $this->publishes([
            __DIR__.'/../resources/css' => resource_path('css/vendor/filament-billing-kit'),
        ], 'filament-billing-kit-assets');

        $this->publishes([
            __DIR__.'/../resources/views' => resource_path('views/vendor/filament-billing-kit'),
        ], 'filament-billing-kit-views');

        FilamentAsset::register([
            Css::make('filament-billing-kit', __DIR__.'/../resources/css/filament-billing-kit.css'),
        ], package: 'vendor/filament-billing-kit');

        // Enregistrement des événements Stripe uniquement si le driver Stripe est actif
        $driverClass = config('filament-billing-kit.payment_driver', StripeDriver::class);

        if (
            $driverClass === StripeDriver::class
            || (is_string($driverClass) && is_subclass_of($driverClass, StripeDriver::class))
        ) {
            $this->registerStripeEvents();
        }
    }

    protected function registerStripeEvents(): void
    {
        if (! class_exists(\Laravel\Cashier\Events\SubscriptionCreated::class)) {
            return;
        }

        Event::listen(
            \Laravel\Cashier\Events\SubscriptionCreated::class,
            [Listeners\StripeEventListener::class, 'handleSubscriptionCreated']
        );
        Event::listen(
            \Laravel\Cashier\Events\SubscriptionUpdated::class,
            [Listeners\StripeEventListener::class, 'handleSubscriptionUpdated']
        );
        Event::listen(
            \Laravel\Cashier\Events\SubscriptionDeleted::class,
            [Listeners\StripeEventListener::class, 'handleSubscriptionDeleted']
        );
        Event::listen(
            \Laravel\Cashier\Events\InvoicePaymentSucceeded::class,
            [Listeners\StripeEventListener::class, 'handleInvoicePaymentSucceeded']
        );
        Event::listen(
            \Laravel\Cashier\Events\InvoicePaymentFailed::class,
            [Listeners\StripeEventListener::class, 'handleInvoicePaymentFailed']
        );
    }
}
