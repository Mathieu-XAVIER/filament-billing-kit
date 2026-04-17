<?php

namespace Mxavier\FilamentBillingKit\Filament\Pages;

use BackedEnum;
use Exception;
use Filament\Pages\Page;
use Illuminate\Database\Eloquent\Model;
use UnitEnum;

class MySubscription extends Page
{
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-credit-card';

    protected static string|UnitEnum|null $navigationGroup = 'Mon compte';

    protected static ?string $navigationLabel = 'Mon abonnement';

    protected static ?int $navigationSort = 10;

    protected string $view = 'filament-billing-kit::pages.my-subscription';

    public function getBillable(): ?Model
    {
        $mode = config('filament-billing-kit.mode', 'mono-tenant');

        if ($mode === 'multi-tenant') {
            return filament()->getTenant();
        }

        return auth()->user();
    }

    public function getSubscription(): mixed
    {
        $billable = $this->getBillable();

        if (! $billable) {
            return null;
        }

        $name = config('filament-billing-kit.subscription_name', 'default');

        return $billable->subscription($name);
    }

    public function getCurrentPlan(): mixed
    {
        $billable = $this->getBillable();

        if (! $billable || ! method_exists($billable, 'currentPlan')) {
            return null;
        }

        return $billable->currentPlan();
    }

    public function redirectToStripePortal(): \Symfony\Component\HttpFoundation\RedirectResponse
    {
        $billable = $this->getBillable();

        $returnRoute = config('filament-billing-kit.billing_return_route', 'filament.admin.pages.my-subscription');

        try {
            $returnUrl = route($returnRoute);
        } catch (Exception) {
            $returnUrl = url('/');
        }

        return $billable->redirectToBillingPortal($returnUrl);
    }
}
