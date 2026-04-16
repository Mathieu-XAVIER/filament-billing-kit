<?php

namespace Mxavier\FilamentBillingKit\Livewire;

use Illuminate\Database\Eloquent\Model;
use Illuminate\View\View;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Mxavier\FilamentBillingKit\Models\Plan;
use Stripe\Exception\ApiErrorException;
use Throwable;

#[Layout('filament-billing-kit::layouts.billing')]
class MySubscriptionPage extends Component
{
    public ?string $errorMessage = null;

    public function getBillable(): ?Model
    {
        $mode = config('filament-billing-kit.mode', 'mono-tenant');

        return $mode === 'multi-tenant'
            ? filament()->getTenant()
            : auth()->user();
    }

    public function getSubscription(): mixed
    {
        $billable = $this->getBillable();

        if (! $billable) {
            return null;
        }

        return $billable->subscription(
            config('filament-billing-kit.subscription_name', 'default')
        );
    }

    public function getCurrentPlan(): ?Plan
    {
        $billable = $this->getBillable();

        if (! $billable || ! method_exists($billable, 'currentPlan')) {
            return null;
        }

        return $billable->currentPlan();
    }

    public function manageBilling(): mixed
    {
        $billable = $this->getBillable();

        if (! $billable) {
            $this->errorMessage = 'Impossible d\'identifier votre compte.';

            return null;
        }

        try {
            $url = $billable->billingPortalUrl(route('billing.subscription'));
        } catch (ApiErrorException $e) {
            $this->errorMessage = 'Erreur Stripe : '.$e->getMessage();

            return null;
        } catch (Throwable $e) {
            $this->errorMessage = 'Une erreur inattendue est survenue. Veuillez réessayer.';
            report($e);

            return null;
        }

        return $this->redirect($url, navigate: false);
    }

    public function render(): View
    {
        return view('filament-billing-kit::livewire.my-subscription-page', [
            'subscription' => $this->getSubscription(),
            'currentPlan' => $this->getCurrentPlan(),
        ]);
    }
}
