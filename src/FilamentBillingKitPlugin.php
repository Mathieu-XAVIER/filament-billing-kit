<?php

namespace Mxavier\FilamentBillingKit;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Mxavier\FilamentBillingKit\Pages\MyInvoices;
use Mxavier\FilamentBillingKit\Pages\MySubscription;
use Mxavier\FilamentBillingKit\Resources\InvoiceResource;
use Mxavier\FilamentBillingKit\Resources\PlanResource;
use Mxavier\FilamentBillingKit\Resources\SubscriptionResource;
use Mxavier\FilamentBillingKit\Widgets\ActiveSubscriptionsWidget;
use Mxavier\FilamentBillingKit\Widgets\FailedPaymentsWidget;
use Mxavier\FilamentBillingKit\Widgets\OngoingTrialsWidget;

class FilamentBillingKitPlugin implements Plugin
{
    protected bool $adminResources = true;

    protected bool $tenantPages = false;

    protected bool $widgets = true;

    public static function make(): static
    {
        return app(static::class);
    }

    public function getId(): string
    {
        return 'filament-billing-kit';
    }

    public function register(Panel $panel): void
    {
        if ($this->adminResources) {
            $panel->resources([
                PlanResource::class,
                SubscriptionResource::class,
                InvoiceResource::class,
            ]);
        }

        if ($this->tenantPages) {
            $panel->pages([
                MySubscription::class,
                MyInvoices::class,
            ]);
        }

        if ($this->widgets && config('filament-billing-kit.enable_widgets', true)) {
            $panel->widgets([
                ActiveSubscriptionsWidget::class,
                OngoingTrialsWidget::class,
                FailedPaymentsWidget::class,
            ]);
        }
    }

    public function boot(Panel $panel): void {}

    public function adminResources(bool $enabled = true): static
    {
        $this->adminResources = $enabled;

        return $this;
    }

    public function tenantPages(bool $enabled = true): static
    {
        $this->tenantPages = $enabled;

        return $this;
    }

    public function widgets(bool $enabled = true): static
    {
        $this->widgets = $enabled;

        return $this;
    }
}
