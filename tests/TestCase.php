<?php

namespace Mxavier\FilamentBillingKit\Tests;

use Mxavier\FilamentBillingKit\FilamentBillingKitServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
    }

    protected function getPackageProviders($app): array
    {
        return [
            FilamentBillingKitServiceProvider::class,
        ];
    }

    protected function defineEnvironment($app): void
    {
        $app['config']->set('database.default', 'testing');
        $app['config']->set('database.connections.testing', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);

        $app['config']->set('filament-billing-kit.mode', 'mono-tenant');
        $app['config']->set('filament-billing-kit.billable_model', Fixtures\User::class);
        $app['config']->set('filament-billing-kit.subscription_name', 'default');
        $app['config']->set('filament-billing-kit.enable_widgets', true);
        $app['config']->set('filament-billing-kit.enable_invoices', true);
        $app['config']->set('filament-billing-kit.enable_entitlements', true);
    }
}
